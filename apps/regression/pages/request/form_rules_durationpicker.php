<?php

  // The fixture of request/form_rules_durationpicker_post: the rules of a duration picker are
  // those of the number of seconds it posts, checked before the page's PHP runs, and this
  // file - running after the post pass - sees only whether the post kept them, and what it
  // stores.

  $state = padPosted ( 'time' ) ? 'stored ' . ( $_POST ['length'] ?? '' ) . ' seconds' : 'not stored';

?>
