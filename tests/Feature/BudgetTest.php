<?php

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use App\Models\Submission;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetTest extends TestCase
{
    use RefreshDatabase;

    protected User $finance;
    protected Category $category;
    protected Budget $budget;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Seed Roles & Permissions
        $this->seed(RoleSeeder::class);

        // 2. Create Finance User
        $this->finance = User::create([
            'name' => 'Fahmi Finance',
            'email' => 'finance@system.com',
            'password' => bcrypt('password'),
        ]);
        $this->finance->assignRole('Finance');

        // 3. Create Category
        $this->category = Category::create([
            'name' => 'Operasional Kantor',
            'code' => 'OPERASIONAL'
        ]);

        // 4. Set Budget limit to 10 Million for 2026
        $this->budget = Budget::create([
            'category_id' => $this->category->id,
            'fiscal_year' => 2026,
            'limit_amount' => 10000000.00,
            'spent_amount' => 0.00,
            'remaining_amount' => 10000000.00,
        ]);
    }

    /**
     * Test successful payment when budget is available.
     */
    public function test_payment_disburses_when_budget_sufficient(): void
    {
        $submission = Submission::create([
            'submission_number' => 'SUB-OPS-001',
            'submission_date' => '2026-07-07', // matching fiscal year 2026
            'user_id' => User::factory()->create()->id,
            'category_id' => $this->category->id,
            'requested_amount' => 4000000.00, // 4 Million (under 10M limit)
            'description' => 'Tinta printer.',
            'status' => 'Waiting Finance'
        ]);

        $response = $this->actingAs($this->finance)->post(route('payments.pay', $submission), [
            'payment_date' => '2026-07-07',
            'reference_number' => 'BANK-TRX-001',
            'notes' => 'Telah dibayar.'
        ]);

        $response->assertRedirect(route('payments.index'));
        $response->assertSessionHas('success');

        $submission->refresh();
        $this->assertEquals('Paid', $submission->status);

        // Verify budget deduction
        $this->budget->refresh();
        $this->assertEquals(4000000.00, (float)$this->budget->spent_amount);
        $this->assertEquals(6000000.00, (float)$this->budget->remaining_amount);

        // Verify payment entry
        $this->assertDatabaseHas('payments', [
            'submission_id' => $submission->id,
            'reference_number' => 'BANK-TRX-001',
            'amount' => 4000000.00
        ]);
    }

    /**
     * Test payment gets rejected when budget is insufficient.
     */
    public function test_payment_fails_and_rejects_when_budget_insufficient(): void
    {
        $submission = Submission::create([
            'submission_number' => 'SUB-OPS-002',
            'submission_date' => '2026-07-07', // matching fiscal year 2026
            'user_id' => User::factory()->create()->id,
            'category_id' => $this->category->id,
            'requested_amount' => 12000000.00, // 12 Million (exceeds 10M limit!)
            'description' => 'Renovasi meja resepsionis.',
            'status' => 'Waiting Finance'
        ]);

        $response = $this->actingAs($this->finance)->post(route('payments.pay', $submission), [
            'payment_date' => '2026-07-07',
            'reference_number' => 'BANK-TRX-002',
            'notes' => 'Coba bayar.'
        ]);

        $response->assertRedirect(route('payments.index'));
        $response->assertSessionHas('error');

        $submission->refresh();
        // Budget checks should automatically transition submission status to Rejected
        $this->assertEquals('Rejected', $submission->status);

        // Verify budget remains untouched
        $this->budget->refresh();
        $this->assertEquals(0.00, (float)$this->budget->spent_amount);
        $this->assertEquals(10000000.00, (float)$this->budget->remaining_amount);

        // Verify NO payment was created
        $this->assertDatabaseMissing('payments', [
            'submission_id' => $submission->id,
        ]);
    }
}
