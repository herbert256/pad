<?php

  // The fixture of request/a_field_a_post_leaves_out_is_checked_from_the_query_string: three
  // fields with rules in the template and one the page's PHP checks with padValidate, and
  // what the page would store - the variables and padRequest, which take a field the post
  // left out from the query string.

  $leftOwn   = padFormCameBack ( 'sign' ) ? padValidate ( [ 'role' => 'in:user,editor' ] ) : [];
  $leftState = ( padPosted ( 'sign' ) and ! $leftOwn )
             ? 'stored website=[' . ( $website ?? '' ) . '] plan=[' . ( $plan ?? '' ) . '] role=[' . padRequest ( 'role' ) . ']'
             : 'not stored';

?>
