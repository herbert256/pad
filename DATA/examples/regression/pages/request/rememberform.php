<?php

  // The fixture of remember_keeps_the_form_token: a token written before the {pad:auth} that
  // logs in the remember-me user, and whether this request was the post back.

  $asked = padRequestMethod () == 'POST' ? 'posted' : 'shown';

?>
