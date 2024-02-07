<?php

namespace DNADesign\IdleLock\Extensions;

use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\FieldType\DBDatetime;
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
     * @param $result
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
        $lowestThreshold = $groups->filter([
            'LockoutThresholdDays:GreaterThan' => 0,
            'LockoutThresholdDays:LessThan' => $defaultLockoutThreshold,
        ])->min('LockoutThresholdDays') ?: $defaultLockoutThreshold;

        return DBDateTime::now()->modify("-{$lowestThreshold} days");
    }

    /**
     * Return the date the user has last accessed the CMS
     */
    public function getLastAccessed() : DBDatetime
    {
        // Default "Last"; i.e. accounts for new users who haven't yet logged in
        $lastAccessed = $this->owner->dbObject('LastEdited');

        // Check for LoginSessions, and overwrite the default "Last" value
        if ($this->owner->LoginSessions()->exists()) {
            $latestLoginSession = $this->owner->LoginSessions()->sort('LastAccessed', 'DESC')->first();
            $lastAccessed = $latestLoginSession->dbObject('LastAccessed');
        }

        return $lastAccessed;
    }

    /**
     * Return whether the user should be locked out
     * Depending on whether they haven't logged in for a certain amount of time.
     */
    public function shouldBeLockedOut() : bool
    {
        $lockIfInactiveAfter = $this->owner->getLockIfInactiveAfter();
        $lastAccessed = $this->owner->getLastAccessed();

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
