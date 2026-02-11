<?php

use PHPUnit\Framework\TestCase;

class EditionsTest extends TestCase
{
    public function testSixMonthsBeforeFestivalReturnsPreviousEdition()
    {
        $current = Editions::current();
        $from = Editions::from($current);

        if ($from === null) {
            $this->markTestSkipped('Current edition has no from date');
        }

        // 6 months before festival start
        $now = clone $from;
        $now->modify('-6 months');

        $result = Editions::lastCompleted($now);
        $this->assertSame($current['number'] - 1, $result['number']);
    }

    public function testWithinSevenDaysReturnsCurrent()
    {
        $current = Editions::current();
        $from = Editions::from($current);

        if ($from === null) {
            $this->markTestSkipped('Current edition has no from date');
        }

        // 3 days before festival start (within 7-day window)
        $now = clone $from;
        $now->modify('-3 days');

        $result = Editions::lastCompleted($now);
        $this->assertSame($current['number'], $result['number']);
    }

    public function testDuringFestivalReturnsCurrent()
    {
        $current = Editions::current();
        $from = Editions::from($current);

        if ($from === null) {
            $this->markTestSkipped('Current edition has no from date');
        }

        // During the festival (1 day after start)
        $now = clone $from;
        $now->modify('+1 day');

        $result = Editions::lastCompleted($now);
        $this->assertSame($current['number'], $result['number']);
    }
}
