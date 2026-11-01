<?php

  // The fixture of request/form_rules_treepicker_post: the rules of a tree picker are those of
  // the one item it posts, checked before the page's PHP runs, and this file - running after
  // the post pass - sees only whether the post kept them, and what it stores.

  $state = padPosted ( 'pick' ) ? 'stored ' . ( $_POST ['topic'] ?? '' ) : 'not stored';

?>
