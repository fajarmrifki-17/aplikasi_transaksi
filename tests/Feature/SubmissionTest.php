<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use App\Models\Submission;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected User $staff;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Seed Roles & Permissions
        $this->seed(RoleSeeder::class);

        // 2. Create Staff user
        $this->staff = User::create([
            'name' => 'Budi Staff',
            'email' => 'staff@system.com',
            'password' => bcrypt('password'),
        ]);
        $this->staff->assignRole('Staff');

        // 3. Create a Category
        $this->category = Category::create([
            'name' => 'Operasional Kantor',
            'code' => 'OPERASIONAL',
            'description' => 'Operasional'
        ]);
    }

    /**
     * Test that staff can create a draft submission with attachments.
     */
    public function test_staff_can_create_draft_submission_with_files(): void
    {
        Storage::fake('private');

        $file = UploadedFile::fake()->create('receipt.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->staff)->post(route('submissions.store'), [
            'category_id' => $this->category->id,
            'requested_amount' => 1500000,
            'description' => 'Pembelian printer baru kantor.',
            'attachments' => [$file]
        ]);

        $response->assertRedirect(route('submissions.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('submissions', [
            'user_id' => $this->staff->id,
            'category_id' => $this->category->id,
            'requested_amount' => 1500000,
            'status' => 'Draft'
        ]);

        $submission = Submission::first();
        $this->assertCount(1, $submission->submissionFiles);
        Storage::disk('private')->assertExists($submission->submissionFiles->first()->file_path);
    }

    /**
     * Test that staff can update a draft submission.
     */
    public function test_staff_can_update_draft_submission(): void
    {
        $submission = Submission::create([
            'submission_number' => 'SUB/20260707/0001',
            'submission_date' => now()->toDateString(),
            'user_id' => $this->staff->id,
            'category_id' => $this->category->id,
            'requested_amount' => 2000000,
            'description' => 'Keperluan tinta printer.',
            'status' => 'Draft'
        ]);

        $response = $this->actingAs($this->staff)->put(route('submissions.update', $submission), [
            'category_id' => $this->category->id,
            'requested_amount' => 2500000,
            'description' => 'Diubah menjadi tinta dan kertas printer.',
        ]);

        $response->assertRedirect(route('submissions.show', $submission));
        $this->assertDatabaseHas('submissions', [
            'id' => $submission->id,
            'requested_amount' => 2500000,
            'description' => 'Diubah menjadi tinta dan kertas printer.',
        ]);
    }
}
