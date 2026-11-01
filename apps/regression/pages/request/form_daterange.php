<?php

  // The fixture of request/form_daterange_post: a date range posts two fields, stay_from and
  // stay_to, which the page's PHP checks with padValidate - its rules= are refused - the
  // departure after the arrival, and stores the nights between them.

  $state = 'not stored';

  if ( padFormCameBack ( 'book' ) and ! padValidate ( [ 'stay_from' => 'required|date', 'stay_to' => 'required|date|after:stay_from' ] ) )
    $state = 'stored ' . ( ( strtotime ( $_POST ['stay_to'] . ' UTC' ) - strtotime ( $_POST ['stay_from'] . ' UTC' ) ) / 86400 ) . ' nights';

?>
