# New Topics Need Approval for phpBB3

phpBB extension that adds a new forum permission to send new topics to moderation.  Once enabled you must set which forums, under the Misc tab of the forum, you want new topics to be on moderator queue on a per forum basis.

[![Build Status](https://github.com/rmcgirr83/newtopicsneedapproval/workflows/Tests/badge.svg)](https://github.com/rmcgirr83/newtopicsneedapproval/actions)

## Installation

### 1. clone
Clone (or download and move) the repository into the folder ext/rmcgirr83/newtopicsneedapproval:

```
cd phpBB3
git clone https://github.com/rmcgirr83/newtopicsneedapproval.git ext/rmcgirr83/newtopicsneedapproval/
```

### 2. activate
Go to admin panel -> tab customise -> Manage extensions -> enable New Topics Need Approval

Within the Admin panel visit the Forums tab and choose which forums, under the Misc tab of the forum, for new topics to put on moderation queue.  The permission will be called `New topics need approval` and has to be set to Yes to have the desired effect. Ensure after installing this extension you purge the cache in the ACP.
