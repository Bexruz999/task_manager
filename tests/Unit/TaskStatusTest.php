<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class TaskStatusTest extends TestCase
{
    public function it_validates_status_transitions(): void
    {
        $currentStatus = 'pending';
        $newStatus = 'done';

        $canTransition = $this->checkTransition($currentStatus, $newStatus);

        $this->assertFalse($canTransition, "Статус «pending» не должен переходить напрямую в статус «done».");
    }

    private function checkTransition($from, $to): bool
    {
        $allowed = [
            'pending'     => ['in_progress', 'cancelled'],
            'in_progress' => ['done', 'pending'],
        ];

        return in_array($to, $allowed[$from] ?? [], true);
    }
}
