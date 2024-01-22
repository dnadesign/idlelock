<?php

namespace DNADesign\IdleLock\Tasks;

use SilverStripe\Dev\BuildTask;
use SilverStripe\Security\Group;

class AutoLockMembersTask extends BuildTask
{
    protected $title = 'Auto Lock Members Task';

    protected $description = 'Automatically locks member accounts based on last login time. Threshold determined per security Group.';

    public function run($request)
    {
        // Loop through all groups
        foreach (Group::get() as $group) {
            // Get threshold time from group configuration (convert days to seconds)
            $thresholdDays = $group->ThresholdDays ?? 30; // Default: 30 days
            $thresholdTime = $thresholdDays * 86400; // Convert days to seconds

            // Calculate the datetime threshold based on LastVisited or LastEdited if LastVisited is null
            $thresholdDatetime = date('Y-m-d H:i:s', strtotime("-{$thresholdDays} days"));

            // Get members of the current group who haven't logged in since the specified time
            $inactiveMembers = $group->Members()
                ->filterAny(
                    [
                    // 'LastVisited:LessThan' => $thresholdDatetime,
                    // 'LastVisited:ExactMatch' => null,
                    'LastEdited:LessThan' => $thresholdDatetime,
                    ]
                );
            foreach ($inactiveMembers as $member) {
                // Lock the member account
                $member->Locked = true;
                $member->write();
            }
        }

        echo 'Member auto-lock task completed.';
    }
}
