<?php

namespace DNADesign\IdleLock\Extensions;

use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\Member;

class MemberLockoutExtension extends DataExtension
{
    private static $db = [
        'Locked' => 'Boolean',
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $defaultLockoutThreshold = Config::inst()->get(Member::class, 'lockout_threshold');
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
     * Return either the access date of the most recent LoginSession, or the created date if there are none
     *
     * @return string
     */
    public function getLastAccessed()
    {
        // Default if there are no login sessions
        $lastAccessed = sprintf('New: %s ', $this->owner->LastEdited);

        // Check for LoginSessions, and overwrite the default "Last" value
        if ($this->owner->LoginSessions()->exists()) {
            $latestLoginSession = $this->owner->LoginSessions()->sort('LastAccessed', 'DESC')->first();
            $lastAccessed = $latestLoginSession->LastAccessed;
        }

        return $lastAccessed;
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
}
