<?php

  // The fixture of request/form_rules_multiselect_post: the rules of a multiselect are the
  // rules of the list it posts, checked before the page's PHP runs, and this file - running
  // after the post pass - sees only whether the post kept them, and the lists it stores.

  $state = padPosted ( 'pick' )
         ? 'stored ' . implode ( ', ', (array) ( $_POST ['interests'] ?? [] ) ) . ' on ' . implode ( ', ', (array) ( $_POST ['days'] ?? [] ) )
         : 'not stored';

?>
