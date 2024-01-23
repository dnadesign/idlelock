# IdleLock
They say that idle hands are the devils playthings, and like so, idle accounts are the security vulnerability's gateway.

## Introduction

Safeguard your SilverStripe site with this module that automatically locks idle accounts, fortifying your security by closing the gateway to potential vulnerabilities. 

The default idle period can be configured, and may be specified per security group.

A Locked User report is included.

## Requirements

* SilverStripe 4.13 / 5.1
* queuedjobs 4.12 / 5.0

## Installation

Add the repository to composer.json
```
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/dnadesign/silverstripe-idlelock.git"
        }
    ],
```
Then, install the module:
`composer require dnadesign/silverstripe-idlelock`

## Configuration

To automatically lock idle accounts, set up a cron to run the task at your desired frequency.
e.g. on Silverstripe Platform, add a cron to **.platform.yml**, ensuring the `queuedjobs_task` is also present:
```
crons:
  queuedjobs_task:
    time: '* * * * *'
    sake_once: 'dev/tasks/ProcessJobQueueTask'
    vhost: 'mysite'
  queuedjobs_task:
    time: '0 2 * * *'
    sake_once: 'dev/tasks/LockMembersTask'
    vhost: 'mysite'
```

To update the global default lockout threshold, set the config:

```
SilverStripe\Security\Member:
  lockout_threshold: 30
```

To set the Security Group specific lockout threshold, update the value for that group in the security admin.

**NB1:** If the user is a member of multiple groups, the lowest threshold will apply.

**NB2:** The group threshold cannot be used to increase the threshold beyond the global default.
