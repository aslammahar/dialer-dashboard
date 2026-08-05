<?php

namespace Tests\Unit;

use App\Services\SalesService;
use PHPUnit\Framework\TestCase;

class SalesServiceTest extends TestCase
{
    public function test_attendance_status_weight_counts_half_day_as_half(): void
    {
        $service = new SalesService();

        $this->assertSame(1.0, $service->attendanceStatusWeight('present'));
        $this->assertSame(0.5, $service->attendanceStatusWeight('half_day'));
        $this->assertSame(0.0, $service->attendanceStatusWeight('absent'));
    }
}
