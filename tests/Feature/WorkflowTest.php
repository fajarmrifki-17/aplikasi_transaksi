<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use App\Models\Submission;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $staff;
    protected User $supervisor;
    protected User $manager;
    protected User $director;
    protected User $finance;
    protected Category $poCategory;
    protected Category $opsCategory;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Seed Roles & Permissions
        $this->seed(RoleSeeder::class);

        // 2. Create Users
        $this->staff = $this->createUser('Staff');
        $this->supervisor = $this->createUser('Supervisor');
        $this->manager = $this->createUser('Manager');
        $this->director = $this->createUser('Director');
        $this->finance = $this->createUser('Finance');

        // 3. Create Categories
        $this->poCategory = Category::create(['name' => 'PO Produk', 'code' => 'PO-PROD']);
        $this->opsCategory = Category::create(['name' => 'Operasional Kantor', 'code' => 'OPERASIONAL']);
    }

    private function createUser(string $role): User
    {
        $user = User::create([
            'name' => "User {$role}",
            'email' => strtolower($role) . '@system.com',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole($role);
        return $user;
    }

    /**
     * Condition 1: Category == "PO Produk" (Staff -> Director -> Finance)
     */
    public function test_workflow_po_product(): void
    {
        $submission = Submission::create([
            'submission_number' => 'SUB-PO-001',
            'submission_date' => now()->toDateString(),
            'user_id' => $this->staff->id,
            'category_id' => $this->poCategory->id,
            'requested_amount' => 4000000,
            'description' => 'PO Produk inventory.',
            'status' => 'Draft'
        ]);

        // Submit submission
        $this->actingAs($this->staff)->post(route('submissions.submit', $submission));
        $submission->refresh();
        
        // Should skip Supervisor and Manager, go directly to Director approval
        $this->assertEquals('Waiting Director Approval', $submission->status);

        // Director Approves
        $this->actingAs($this->director)->post(route('approvals.action', $submission), [
            'action' => 'Approve',
            'notes' => 'PO disetujui.'
        ]);
        $submission->refresh();

        // Should go to Waiting Finance
        $this->assertEquals('Waiting Finance', $submission->status);
    }

    /**
     * Condition 2: Category != "PO Produk" AND Amount <= 5,000,000 (Staff -> Supervisor -> Finance)
     */
    public function test_workflow_low_amount(): void
    {
        $submission = Submission::create([
            'submission_number' => 'SUB-OPS-001',
            'submission_date' => now()->toDateString(),
            'user_id' => $this->staff->id,
            'category_id' => $this->opsCategory->id,
            'requested_amount' => 3000000, // <= 5 Million
            'description' => 'Operasional kantor.',
            'status' => 'Draft'
        ]);

        $this->actingAs($this->staff)->post(route('submissions.submit', $submission));
        $submission->refresh();
        $this->assertEquals('Waiting Supervisor Approval', $submission->status);

        // Supervisor Approves
        $this->actingAs($this->supervisor)->post(route('approvals.action', $submission), [
            'action' => 'Approve',
            'notes' => 'Disetujui.'
        ]);
        $submission->refresh();

        // Skips Manager and Director, goes straight to Finance
        $this->assertEquals('Waiting Finance', $submission->status);
    }

    /**
     * Condition 3: Category != "PO Produk" AND Amount > 5,000,000 AND Amount <= 10,000,000 (Staff -> Supervisor -> Manager -> Finance)
     */
    public function test_workflow_medium_amount(): void
    {
        $submission = Submission::create([
            'submission_number' => 'SUB-OPS-002',
            'submission_date' => now()->toDateString(),
            'user_id' => $this->staff->id,
            'category_id' => $this->opsCategory->id,
            'requested_amount' => 7000000, // 5M to 10M
            'description' => 'Operasional AC kantor.',
            'status' => 'Draft'
        ]);

        $this->actingAs($this->staff)->post(route('submissions.submit', $submission));
        $submission->refresh();
        $this->assertEquals('Waiting Supervisor Approval', $submission->status);

        // Supervisor Approves
        $this->actingAs($this->supervisor)->post(route('approvals.action', $submission), [
            'action' => 'Approve',
            'notes' => 'SPV approves.'
        ]);
        $submission->refresh();
        $this->assertEquals('Waiting Manager Approval', $submission->status);

        // Manager Approves
        $this->actingAs($this->manager)->post(route('approvals.action', $submission), [
            'action' => 'Approve',
            'notes' => 'Manager approves.'
        ]);
        $submission->refresh();

        // Skips Director, goes straight to Finance
        $this->assertEquals('Waiting Finance', $submission->status);
    }

    /**
     * Condition 4: Amount > 10,000,000 (Staff -> Supervisor -> Manager -> Director -> Finance)
     */
    public function test_workflow_high_amount(): void
    {
        $submission = Submission::create([
            'submission_number' => 'SUB-OPS-003',
            'submission_date' => now()->toDateString(),
            'user_id' => $this->staff->id,
            'category_id' => $this->opsCategory->id,
            'requested_amount' => 15000000, // > 10M
            'description' => 'Renovasi ruangan kantor.',
            'status' => 'Draft'
        ]);

        $this->actingAs($this->staff)->post(route('submissions.submit', $submission));
        $submission->refresh();
        $this->assertEquals('Waiting Supervisor Approval', $submission->status);

        // Supervisor Approves
        $this->actingAs($this->supervisor)->post(route('approvals.action', $submission), [
            'action' => 'Approve',
            'notes' => 'OK'
        ]);
        $submission->refresh();
        $this->assertEquals('Waiting Manager Approval', $submission->status);

        // Manager Approves
        $this->actingAs($this->manager)->post(route('approvals.action', $submission), [
            'action' => 'Approve',
            'notes' => 'OK'
        ]);
        $submission->refresh();
        $this->assertEquals('Waiting Director Approval', $submission->status);

        // Director Approves
        $this->actingAs($this->director)->post(route('approvals.action', $submission), [
            'action' => 'Approve',
            'notes' => 'OK'
        ]);
        $submission->refresh();

        // Finally to Finance
        $this->assertEquals('Waiting Finance', $submission->status);
    }
}
