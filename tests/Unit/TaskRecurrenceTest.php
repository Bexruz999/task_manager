<?php

namespace Tests\Unit;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class TaskRecurrenceTest extends TestCase
{
    public function test_it_calculates_next_deadline_correctly(): void
    {
        $initialDeadline = Carbon::parse('2026-02-15 10:00:00');

        $nextDaily = $initialDeadline->copy()->addDay();
        $this->assertEquals('2026-02-16 10:00:00', $nextDaily->toDateTimeString());

        $nextWeekly = $initialDeadline->copy()->addWeek();
        $this->assertEquals('2026-02-22 10:00:00', $nextWeekly->toDateTimeString());
    }
}
