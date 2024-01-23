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

    public function canLogIn(&$result)
    {
        if ($this->owner->Locked) {
            $result->addError('Your account has been locked due to inactivity. If this is not correct, please contact the administrator.');
        }
    }
}
