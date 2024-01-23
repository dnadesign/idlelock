<?php

namespace DNADesign\IdleLock\Extensions;

use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\Member;

class GroupLockoutThresholdExtension extends DataExtension
{
    private static $db = [
        'LockoutThreshold' => 'Int', // Threshold time in days
    ];

    /**
     * Update Fields
     * @return FieldList
     */
    public function updateCMSFields(FieldList $fields)
    {
        $defaultLockoutThreshold = Config::inst()->get(Member::class, 'lockout_threshold');

        $fields->insertAfter(
            'Description',
            NumericField::create('LockoutThreshold', 'Lockout threshold')
                ->setDescription(sprintf('Inactive time in days. Members of this group will be locked out if they do not log in for this amount of time.<br>Set to 0 to use the default of %s days.', $defaultLockoutThreshold))
                ->setAttribute('max', 999)
        );
    }
}
