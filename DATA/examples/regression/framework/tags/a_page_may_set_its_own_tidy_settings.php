<?php

  // PAD's own tidy pass, with a sanitize flag, set by the page itself - the fixture that
  // my_tidy_applies_the_sanitize_flags_it_is_given fetches with the pass running. Fetched
  // by the suite with padInclude, the pass is skipped and the tab stays.

  $padTidy           = FALSE;
  $padMyTidy         = TRUE;
  $padMyTidySanitize = [ 'STRIP_LOW' ];

?>
