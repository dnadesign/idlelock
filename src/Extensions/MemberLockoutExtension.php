<?php

namespace DNADesign\IdleLock\Extensions;

use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\Security\LoginAttempt;
use SilverStripe\Security\Member;

class MemberLockoutExtension extends DataExtension
{
    private static $db = [
        'Locked' => 'Boolean',
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $defaultLockoutThreshold = Config::inst()->get(Member::class, 'lockout_threshold_days');
        $fields->replaceField('Locked', CheckboxField::create('Locked')->setDescription(sprintf('When checked, this user cannot log in. Set either manually here, or by idle timeout (default %s days or specified per security group).', $defaultLockoutThreshold)));
    }

    /**
     * Prevent login if the Member is locked out
     *
     * @param  $result
     * @return void
     */
    public function canLogIn(&$result)
    {
        if ($this->owner->Locked) {
            $result->addError('Your account has been locked due to inactivity. If this is not correct, please contact the administrator.');
        }
    }

    /**
     * List the groups this Member is a member of
     *
     * @return string
     */
    public function getGroupNames()
    {
        $groups = $this->owner->Groups()->sort('Title ASC');

        $groupNames = $groups->map('ID', 'Title')->toArray();
        $groupNamesString = implode(' | ', $groupNames);

        return $groupNamesString;
    }

    /**
     * Return the Date after which a user should be locked out
     */
    public function getLockIfInactiveAfter() : DBDateTime
    {
        $defaultLockoutThreshold = Config::inst()->get(Member::class, 'lockout_threshold_days');

        $groups = $this->owner->Groups();
        $lowestThreshold = $groups->filter(
            [
            'LockoutThresholdDays:GreaterThan' => 0,
            'LockoutThresholdDays:LessThan' => $defaultLockoutThreshold,
            ]
        )->min('LockoutThresholdDays') ?: $defaultLockoutThreshold;

        return DBDateTime::now()->modify("-{$lowestThreshold} days");
    }

    /**
     * Return the date the user has last accessed the CMS
     *
     * @return DBDatetime|null
     */
    public function getLastAccessed(): ?DBDateTime
    {
        if ($lastTime = $this->owner->getLastLogin()) {
            return $lastTime;
        }

        return null;
    }

    /**
     * Get the last login attempt
     *
     * @return DBDatetime|null
     */
    public function getLastLogin(): ?DBDateTime
    {
        $lastTime = LoginAttempt::get()
            ->filter(
                [
                    'MemberID' => $this->owner->ID,
                    'Status' => 'Success',
                    ]
            )
            ->sort('Created', 'DESC')
            ->first();

        if ($lastTime) {
            return $lastTime->dbObject('Created');
        }

        return null;
    }

    /**
     * Return whether the user should be locked out
     * Depending on whether they haven't logged in for a certain amount of time.
     */
    public function shouldBeLockedOut() : bool
    {
        $lockIfInactiveAfter = $this->owner->getLockIfInactiveAfter();
        $lastAccessed = $this->owner->getLastLogin();

        // For accounts for new users who haven't logged in yet, use the created date
        if (is_null($lastAccessed)) {
            $lastAccessed = $this->owner->dbObject('Created');
        }

        return $lockIfInactiveAfter > $lastAccessed;
    }

    /**
     * Lock the user if their last login was more than the lockout threshold ago
     *
     * @return boolean
     */
    public function doLockOutAfterIdle() : bool
    {
        $this->owner->Locked = true;
        $this->owner->write();

        return true;
    }
}
