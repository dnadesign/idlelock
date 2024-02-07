<?php

namespace DNADesign\IdleLock\Tasks;

use Psr\Log\LoggerInterface;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\BuildTask;
use SilverStripe\Security\Member;

class LockMembersTask extends BuildTask
{
    protected $title = 'Lock Members Task';

    protected $description = 'Locks member accounts based on last login time. Lockout threshold determined per security Group.';

    public function run($request)
    {
        Injector::inst()->get(LoggerInterface::class)->info('Check for idle member accounts...');

        foreach (Member::get() as $member) {
            if ($member->shouldBeLockedOut()) {
                $member->doLockOutAfterIdle();
                Injector::inst()->get(LoggerInterface::class)->info(sprintf('Member %s (%s) is now locked out of the CMS after reaching the idle lockout threshold', $member->getTitle(), $member->ID));
            }
        }

        exit;
    }
}
