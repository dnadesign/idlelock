<?php

namespace DNADesign\IdleLock\Extensions;

use SilverStripe\ORM\DataExtension;

class GroupLockoutThresholdExtension extends DataExtension
{
    private static $db = [
        'ThresholdTime' => 'Int', // Threshold time in days
    ];
}
