<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AnalyticsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalUsers = User::count();

        $totalCourses = Course::count();

        $totalEnrollments = Enrollment::count();

        $totalRevenue = Transaction::where('status', 'completed')->sum('amount');

        $latestTransaction = Transaction::with(['user', 'course'])->latest()->first();
        $latestActivity = 'No recent activity';

        if ($latestTransaction) {
            $userName = $latestTransaction->user?->full_name ?? 'User';
            $courseTitle = $latestTransaction->course?->title ?? 'Course';
            $latestActivity = "{$userName} bought {$courseTitle}";
        }

        return [
            Stat::make('Total Users', number_format($totalUsers))
                ->description('Registered accounts')
                ->color('info'),

            Stat::make('Total Courses', number_format($totalCourses))
                ->description('Available courses')
                ->color('success'),

            Stat::make('Total Enrollments', number_format($totalEnrollments))
                ->description('Enrolled students')
                ->color('warning'),

            Stat::make('Total Revenue', '$' . number_format($totalRevenue, 2))
                ->description('Gross income from sales')
                ->color('success'),

            Stat::make('Latest Activity', $latestActivity)
                ->description('Real-time update')
                ->color('primary'),
        ];
    }
}
