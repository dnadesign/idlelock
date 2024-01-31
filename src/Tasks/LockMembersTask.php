<?php

namespace DNADesign\IdleLock\Tasks;

use Psr\Log\LoggerInterface;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\BuildTask;
use SilverStripe\Security\Member;

class LockMembersTask extends BuildTask
{
    protected $title = 'Lock Members Task';

    protected $description = 'Locks member accounts based on last login time. Threshold determined per security Group.';

    public function run($request)
    {
        Injector::inst()->get(LoggerInterface::class)->info('Check for idled member accounts...');

        foreach (Member::get() as $member) {
            if ($member->shouldBeLockedOut()) {
                $member->doLockOutAfterIdle();
                Injector::inst()->get(LoggerInterface::class)->info(sprintf('Member %s (%s) is now locked out of the CMS after not logging in for a while', $member->getTitle(), $member->ID));
            }
        }

        exit;
    }
}
