<?php

namespace App\Http\Controllers;

use App\Livewire\CommitteeReviewQueue;
use App\Livewire\StudentDashboard;
use Illuminate\Http\Request;

/**
 * Dispatches users to their respective primary dashboard upon visiting `/dashboard`.
 *
 * Routing Rules:
 * - Reviewers (committee), Administrators (admin), Chairperson (is_chairperson), and Developers (is_developer)
 *   access the institutional Committee Review Queue (`/dashboard`).
 * - Students and other authenticated users access the Student Dashboard (`/dashboard`).
 */
class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        if ($user->hasAnyRole(['admin', 'committee', 'secretary']) || $user->is_chairperson || $user->is_developer) {
            return app(CommitteeReviewQueue::class)();
        }

        return app(StudentDashboard::class)();
    }
}
