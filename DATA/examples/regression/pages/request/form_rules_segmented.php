<?php

  // The fixture of request/form_rules_segmented_post: the rules of a segmented are those of
  // the one choice it posts, checked before the page's PHP runs, and this file - running after
  // the post pass - sees only whether the post kept them, and what it stores.

  $state = padPosted ( 'pick' )
         ? 'stored ' . ( $_POST ['size'] ?? '' ) . ' as ' . ( $_POST ['view'] ?? '' )
         : 'not stored';

?>
