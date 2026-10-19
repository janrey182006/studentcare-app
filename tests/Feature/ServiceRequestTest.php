<?php

namespace Tests\Feature;

use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceRequestTest extends TestCase
{
    use RefreshDatabase;

    private ServiceCategory $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite(); // views use @vite; no need to run npm build for tests
        // Turn off OTP + reCAPTCHA so tests don't need email or Google.
        config(['services.otp.enabled' => false, 'services.recaptcha.enabled' => false]);
        $this->category = ServiceCategory::create(['name' => 'Registrar Services']);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'service_category_id' => $this->category->id,
            'subject' => 'Need my transcript',
            'description' => 'Please prepare my transcript of records for scholarship use.',
            'priority' => 'normal',
        ], $overrides);
    }

    private function makeRequest(User $owner, array $attrs = []): ServiceRequest
    {
        return ServiceRequest::create(array_merge([
            'user_id' => $owner->id,
            'service_category_id' => $this->category->id,
            'subject' => 'Sample',
            'description' => 'A sample description that is long enough.',
        ], $attrs));
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/requests')->assertRedirect('/login');
    }

    public function test_student_can_submit_a_request(): void
    {
        $student = User::factory()->create();

        $this->actingAs($student)->post('/requests', $this->payload())->assertRedirect();

        $this->assertDatabaseHas('service_requests', ['subject' => 'Need my transcript', 'user_id' => $student->id, 'status' => 'pending']);
    }

    public function test_validation_rejects_short_description_and_keeps_old_input(): void
    {
        $student = User::factory()->create();

        $this->actingAs($student)->from('/requests/create')
            ->post('/requests', $this->payload(['description' => 'too short']))
            ->assertSessionHasErrors('description')
            ->assertRedirect('/requests/create');

        $this->assertDatabaseCount('service_requests', 0);
    }

    public function test_student_cannot_view_another_students_request(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $request = $this->makeRequest($owner);

        $this->actingAs($other)->get("/requests/{$request->id}")->assertForbidden();
        $this->actingAs($owner)->get("/requests/{$request->id}")->assertOk();
    }

    public function test_student_cannot_open_reports_or_admin_pages(): void
    {
        $student = User::factory()->create();

        $this->actingAs($student)->get('/reports')->assertForbidden();
        $this->actingAs($student)->get('/admin/categories')->assertForbidden();
        $this->actingAs($student)->get('/admin/users')->assertForbidden();
    }

    public function test_student_cannot_change_status(): void
    {
        $student = User::factory()->create();
        $request = $this->makeRequest($student);

        $this->actingAs($student)->patch("/requests/{$request->id}/status", ['status' => 'resolved'])->assertForbidden();
        $this->assertSame('pending', $request->fresh()->status);
    }

    public function test_staff_can_update_status_and_history_is_logged(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $student = User::factory()->create();
        $request = $this->makeRequest($student);

        $this->actingAs($staff)->patch("/requests/{$request->id}/status", ['status' => 'in_progress', 'assigned_to' => $staff->id])
            ->assertSessionHasNoErrors();

        $this->assertSame('in_progress', $request->fresh()->status);
        $this->assertDatabaseHas('request_updates', ['service_request_id' => $request->id, 'status' => 'in_progress']);
    }

    public function test_rejecting_requires_a_reason(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $request = $this->makeRequest(User::factory()->create());

        $this->actingAs($staff)->patch("/requests/{$request->id}/status", ['status' => 'rejected'])
            ->assertSessionHasErrors('message');
    }

    public function test_admin_can_manage_categories_and_staff_cannot(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff)->post('/admin/categories', ['name' => 'Test'])->assertForbidden();
        $this->actingAs($admin)->post('/admin/categories', ['name' => 'Test'])->assertRedirect();
        $this->assertDatabaseHas('service_categories', ['name' => 'Test']);
    }

    public function test_search_and_status_filter_work(): void
    {
        $student = User::factory()->create();
        $this->makeRequest($student, ['subject' => 'Alpha scholarship']);
        $this->makeRequest($student, ['subject' => 'Beta wifi', 'status' => 'resolved']);

        $this->actingAs($student)->get('/requests?q=scholarship')->assertSee('Alpha scholarship')->assertDontSee('Beta wifi');
        $this->actingAs($student)->get('/requests?status=resolved')->assertSee('Beta wifi')->assertDontSee('Alpha scholarship');
    }

    public function test_public_tracking_hides_personal_data(): void
    {
        $student = User::factory()->create(['name' => 'Secret Person']);
        $request = $this->makeRequest($student);

        $this->get('/track?ref=' . $request->reference_no)->assertOk()->assertSee($request->subject)->assertDontSee('Secret Person');
    }
}
