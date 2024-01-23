<?php

namespace DNADesign\IdleLock\Tasks;

use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\BuildTask;
use SilverStripe\Security\Member;

class LockMembersTask extends BuildTask
{
    protected $title = 'Lock Members Task';

    protected $description = 'Locks member accounts based on last login time. Threshold determined per security Group.';

    public function run($request)
    {
        // Global lockout threshold
        $defaultLockoutThreshold = Config::inst()->get(Member::class, 'lockout_threshold');

        // Loop through all Members
        foreach (Member::get() as $member) {
            $lowestThreshold = $defaultLockoutThreshold;

            // Get the lowest non-0 threshold from the Members groups, and format it for compare
            $groups = $member->Groups();
            $lowestThreshold = $groups->filter('LockoutThreshold:GreaterThan', 0)->min('LockoutThreshold') ?: $lowestThreshold;
            $thresholdDateTime = date('Y-m-d H:i:s', strtotime("-{$lowestThreshold} days"));

            // Default "Last"; accounts for new users who haven't yet logged in
            $lastAccessed = $member->LastEdited;

            // Check for LoginSessions, and overwrite the default "Last" value
            if ($member->LoginSessions()->exists()) {
                $latestLoginSession = $member->LoginSessions()->sort('LastAccessed', 'DESC')->first();
                $lastAccessed = $latestLoginSession->LastAccessed;
            }

            // If the threshold is met, lock the Member account
            if ($thresholdDateTime > $lastAccessed) {
                $member->Locked = true;
                $member->write();
            }
        }

        exit;
    }
}
