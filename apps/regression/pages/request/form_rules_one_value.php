<?php

  // The fixture of request/form_rules_one_value_post: two fields of one value each, with
  // rules, and what the page would store.

  $oneState = padPosted ( 'one' ) ? 'stored a ' . gettype ( $name ) . ' and a ' . gettype ( $color ) : 'not stored';

?>
