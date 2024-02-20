# IdleLock
They say that idle hands are the devils playthings, so idle accounts are the security vulnerability's gateway.

## Introduction

Safeguard your SilverStripe site with this module that automatically locks idle accounts, fortifying your security by closing the gateway to potential vulnerabilities. 

The default idle period can be configured, and may be specified per security group.

A Locked User report is included.

## Requirements

* SilverStripe ^4.13 || ^5.1

## Installation

`composer require dnadesign/silverstripe-idlelock`

## Configuration

To automatically lock idle accounts, set up a cron to run the LockMembersTask task at your desired frequency, e.g. daily

To update the global default lockout threshold, set the config in your project:

```
SilverStripe\Security\Member:
  lockout_threshold_days: 30
```

## Usage

Once the cron is set, the users will automatically lock if they don't lock in for a period longer than the lockout threshold.

Locked users will see a different message on the login screen indicating why they can't log in.

To unlock the user, an admin must view the Member record in the CMS and uncheck the 'Locked' checkbox.

To set a custom lockout threshold for members of a group, update the LockoutThresholdDays field for that Group in the security admin. 
If the user is a member of multiple groups, the lowest threshold will apply.
The group threshold cannot be used to increase the threshold beyond the global default.
