# IdleLock

Safeguard your SilverStripe site by automatically locking idle member accounts. 

The default threshold can be configured, and may be specified per security group.

A Locked User report is included.

## Requirements

* SilverStripe ^4.13 || ^5.1

## Installation

`composer require dnadesign/silverstripe-idlelock`

## Configuration

To automatically lock idle member accounts, set up a cron to run the **LockMembersTask** task at your desired frequency.

To set a global default lockout threshold, set the config in your project:

```
SilverStripe\Security\Member:
  lockout_threshold_days: 30
```

To allow users to be exempt from lockout, controlled by a checkbox on the member profile or a security group, set the config in your project: 

```
SilverStripe\Security\Member:
  lockout_exempt: true

SilverStripe\Security\Group:
  lockout_exempt: true
```

To update the default message shown to locked out users at the login screen, use the LockoutMessage field in the CMS > Settings > Access tab

## Usage

Once the cron is set, unless exempt, member accounts will automatically lock if they don't log in for a period longer than the lockout threshold.

Locked users will see a message on the login screen indicating why they can't log in.

To unlock the user, an admin must view the *Member* record in the CMS and uncheck the 'Locked' checkbox.

To set a custom lockout threshold for members of a group, update the *LockoutThresholdDays* field for that Group in the security admin. 
* If the user is a member of multiple groups, the lowest threshold will apply.
* The group threshold cannot be used to increase the threshold beyond the global default.

Use the *LockoutExempt* field on a Member or a Group to excuse the member or members of the group from the lockout feature.
