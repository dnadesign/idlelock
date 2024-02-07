<?php

namespace DNADesign\IdleLock\Tasks;

use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\Security\Member;

/**
 * This is in lieu of proper unit test as I can't seem to be able to make them work.
 * We'll revisit when we ge the time.
 */
class UnitTestTask extends BuildTask
{
    private static $segment = 'idlelock-unittest';

    protected $enabled = false;

    public function run($request)
    {
        echo 'testDefaultThreshold => '.$this->testDefaultThreshold().PHP_EOL;
        echo 'testNotLockedOut => '.$this->testNotLockedOut().PHP_EOL;
        echo 'testLockedOut => '.$this->testLockedOut().PHP_EOL;

        echo 'Done.';
    }

    private function testDefaultThreshold()
    {
        $threshold = (int) Member::config()->get('lockout_threshold_days');
        return 30 === $threshold;
    }

    private function testNotLockedOut()
    {
        DBDatetime::set_mock_now('2024-02-01 10:00:00');

        $member = new Member();
        $member->LastEdited = '2024-01-15 10:00:00';

        return $member->shouldBeLockedOut() === false;
    }

    private function testLockedOut()
    {
        DBDatetime::set_mock_now('2024-02-01 10:00:00');

        $member = new Member();
        $member->LastEdited = '2023-01-01 10:00:00';

        return $member->shouldBeLockedOut() === true;
    }

    // NOTE: cannot test the group threshold as it would require
    // writing in the database.
}
