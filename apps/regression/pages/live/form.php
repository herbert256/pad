<?php

  // A live region holding a posting form, on a page that has $padCsrf on (the application's
  // _config/config.php switches it on for this page): live/a_posting_form_in_a_live_answer_keeps_its_csrf_token
  // fetches it and posts an event.

  $n = ( padLiveEvent () == 'inc' ) ? 2 : 1;

?>
