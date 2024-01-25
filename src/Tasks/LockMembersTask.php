<?php

namespace DNADesign\IdleLock\Tasks;

use SilverStripe\Dev\BuildTask;
use SilverStripe\Security\Member;

class LockMembersTask extends BuildTask
{
    protected $title = 'Lock Members Task';

    protected $description = 'Locks member accounts based on last login time. Threshold determined per security Group.';

    public function run($request)
    {
        foreach (Member::get() as $member) {
            $member->IdleUserLock();
        }

        exit;
    }
}
