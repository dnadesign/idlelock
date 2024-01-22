# IdleLock
Idle accounts are the security vulnerability's gateway

## Introduction

Safeguard your SilverStripe platform with this module that automatically locks idle accounts, fortifying your security by closing the gateway to potential vulnerabilities. The default idle period can be configured, and/or specified per security group.

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
    sake_once: 'dev/tasks/AutoLockMembersTask'
    vhost: 'mysite'
```
