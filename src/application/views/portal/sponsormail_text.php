<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>Hello,

A guest named <?php echo $name; ?> has requested access to the guest network.

The user has provided the following email address: <?php echo $email; ?>.

Please review this request and apply one of the following actions. If you decide to ignore this email, the guest will not be able to access the guest network and the request will be deleted after 24 hours.
Please visit one of the following links either by clicking or by copy and pasting into your web browser.

Approve for today: <?php echo site_url('sponsor/today/'.$gid) ?>

Approve for this week: <?php echo site_url('sponsor/week/'.$gid) ?>


If you think this an invalid request, you can also reject it now. You can also do this later even after approving it.

Reject or Revoke: <?php echo site_url('sponsor/reject/'.$gid) ?>


This email was sent to you because the guest has specified you as his sponsor during registration.
