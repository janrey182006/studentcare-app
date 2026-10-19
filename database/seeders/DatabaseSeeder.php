<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // NOTE: all names, emails and IDs below are FICTIONAL demo data.
        $password = Hash::make('Password123!');

        $categories = collect([
            ['Registrar Services', "Registrar's Office", 'Transcript, certification, enrollment records and corrections.'],
            ['Scholarship & Financial Aid', 'Scholarship Office', 'Scholarship applications, renewals, and payment concerns.'],
            ['Guidance & Counseling', 'Guidance Office', 'Counseling appointments and student welfare concerns.'],
            ['IT Support & Accounts', 'IT Services', 'Portal accounts, Wi-Fi, lab and email problems.'],
            ['Library Services', 'Library', 'Borrowing, clearance, and research assistance.'],
            ['Facilities & Maintenance', 'Admin Office', 'Report broken facilities or request a room.'],
            ['Student Affairs & ID', 'Student Affairs', 'ID replacement, organizations and activity permits.'],
        ])->map(fn ($c) => ServiceCategory::create(['name' => $c[0], 'office' => $c[1], 'description' => $c[2]]));

        User::create(['name' => 'System Admin', 'email' => 'admin@studentcare.test', 'password' => $password, 'role' => 'admin']);
        $staff = collect([
            ['Maria Santos', 'staff1@studentcare.test'],
            ['Jose Reyes', 'staff2@studentcare.test'],
        ])->map(fn ($s) => User::create(['name' => $s[0], 'email' => $s[1], 'password' => $password, 'role' => 'staff']));

        $students = collect([
            ['Ana Dela Cruz', 'student@studentcare.test', '2026-0001', 'BS Information Technology'],
            ['Mark Villanueva', 'mark@studentcare.test', '2026-0002', 'BS Information Technology'],
            ['Liza Ramos', 'liza@studentcare.test', '2026-0003', 'BS Education'],
            ['Paolo Mendoza', 'paolo@studentcare.test', '2026-0004', 'BS Business Administration'],
            ['Grace Bautista', 'grace@studentcare.test', '2026-0005', 'BS Nursing'],
        ])->map(fn ($s) => User::create([
            'name' => $s[0], 'email' => $s[1], 'student_id' => $s[2], 'course' => $s[3],
            'password' => $password, 'role' => 'student',
        ]));

        $subjects = [
            'Request for Transcript of Records', 'Correction of name in my records', 'Scholarship renewal question',
            'Cannot log in to student portal', 'Wi-Fi not working in Room 204', 'Book clearance request',
            'Replacement of lost school ID', 'Schedule a counseling session', 'Broken aircon in the library',
            'Request for certificate of enrollment', 'Late payment concern', 'Request to use the AVR for an event',
        ];
        $statuses = ['pending', 'pending', 'in_review', 'in_progress', 'in_progress', 'resolved', 'resolved', 'resolved', 'rejected', 'cancelled'];
        $priorities = ['low', 'normal', 'normal', 'normal', 'high', 'urgent'];

        // 45 sample requests spread across the last 6 months, so the charts look meaningful
        for ($i = 0; $i < 45; $i++) {
            $created = now()->subDays(random_int(0, 170))->subHours(random_int(0, 20));
            $status = Arr::random($statuses);
            $student = $students->random();
            $handler = $status === 'pending' ? null : $staff->random();
            $resolvedAt = in_array($status, ['resolved', 'rejected', 'cancelled']) ? (clone $created)->addHours(random_int(4, 120)) : null;

            $request = ServiceRequest::create([
                'user_id' => $student->id,
                'service_category_id' => $categories->random()->id,
                'assigned_to' => $handler?->id,
                'subject' => Arr::random($subjects),
                'description' => 'This is a sample request created for demonstration purposes. Please assist me with this concern as soon as possible.',
                'priority' => Arr::random($priorities),
                'status' => $status,
                'rating' => $status === 'resolved' ? random_int(3, 5) : null,
                'feedback' => $status === 'resolved' ? Arr::random(['Fast and helpful!', 'Thank you.', 'Issue solved, great service.', null]) : null,
                'resolved_at' => $resolvedAt,
                'created_at' => $created,
                'updated_at' => $resolvedAt ?? $created,
            ]);

            $request->updates()->create([
                'user_id' => $student->id, 'status' => 'pending', 'message' => 'Request submitted.',
                'created_at' => $created, 'updated_at' => $created,
            ]);

            if ($handler && $status !== 'cancelled') {
                $request->updates()->create([
                    'user_id' => $handler->id, 'status' => $status,
                    'message' => 'Status changed to ' . $request->statusLabel() . '.',
                    'created_at' => $resolvedAt ?? $created->copy()->addHours(3), 'updated_at' => $resolvedAt ?? $created->copy()->addHours(3),
                ]);
            }
        }
    }
}
