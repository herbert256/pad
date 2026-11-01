<?php

  // The fixture of request/form_rules_that_compare_and_count_post: the rules of the second
  // round on the fields of the template - different, multiple_of (the bottles a number by
  // it alone), gt, after_or_equal, the list toppings[] with min_items and max_items,
  // max_words - checked before this file runs.

  $partyState = padPosted ( 'party' ) ? 'stored ' . implode ( ', ', (array) padRequest ( 'toppings' ) ) : 'not stored';

?>
