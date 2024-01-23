<?php

namespace DNADesign\IdleLock\Reports;

use SilverStripe\Forms\GridField\GridFieldExportButton;
use SilverStripe\Forms\GridField\GridFieldPageCount;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use SilverStripe\Forms\GridField\GridFieldPrintButton;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\Reports\Report;
use SilverStripe\Security\Member;

/**
 * List users and their locked status
 */
class LockedUserReport extends Report
{
    protected $title = 'Locked users Report';

    protected $description = 'Report on users and lock status';

    /**
     * Get the source records for the report, including user data.
     *
     * @param  array|null $params
     * @param  array|null $sort
     * @param  int|null   $limit
     * @return DataList
     */
    public function sourceRecords($params = null, $sort = null, $limit = null)
    {
        // Fetch user data
        $users = Member::get()->leftJoin('Group_Members', 'Group_Members.MemberID = Member.ID');

        // Apply sorting
        if ($sort && is_array($sort)) {
            foreach ($sort as $sortField => $sortDir) {
                $users = $users->sort($sortField, $sortDir);
            }
        }

        return $users;
    }

    /**
     * Get the columns for the report.
     *
     * @return array
     */
    public function columns()
    {
        return [
            'FirstName' => 'First Name',
            'Surname' => 'Last Name',
            'Email' => 'Email',
            'Locked' => 'Locked',
            'GroupsList' => 'Groups',
            'LastVisited' => 'Last Logged In',
        ];
    }

    /**
     * Get the report field with added export button.
     *
     * @return GridField
     */
    public function getReportField()
    {
        $field = parent::getReportField();

        // Add export button
        $field->getConfig()
            ->addComponent(GridFieldExportButton::create('buttons-before-left'))
            ->removeComponentsByType(GridFieldPrintButton::class)
            ->removeComponentsByType(GridFieldPageCount::class)
            ->removeComponentsByType(GridFieldPaginator::class);

        return $field;
    }

    /**
     * Format the list of groups for a user.
     *
     * @param  DataObject $record
     * @return string
     */
    protected function formatGroupsList($record)
    {
        return implode(', ', $record->Groups()->column('Title'));
    }

    /**
     * Format the last visited date and time for a user.
     *
     * @param  DataObject $record
     * @return string
     */
    protected function formatLastVisited($record)
    {
        $lastVisited = $record->LastVisited ?: 'N/A';
        return DBDatetime::create()->setValue($lastVisited)->Nice();
    }
}
