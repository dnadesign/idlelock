<?php

namespace DNADesign\IdleLock\Extensions;

use SilverStripe\ORM\DataExtension;

class MemberExtension extends DataExtension
{
    private static $db = [
        'Locked' => 'Boolean',
    ];
}
