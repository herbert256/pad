<?php

  // The fixture of request/form_rangeslider_post: a range slider posts two fields, price_from
  // and price_to, which the page's PHP checks with padValidate - its rules= are refused - and
  // stores lowest first: a form posted without the script can bring the two crossed.

  $state = 'not stored';

  if ( padFormCameBack ( 'filter' ) and ! padValidate ( [ 'price_from' => 'required|integer', 'price_to' => 'required|integer' ] ) )
    $state = 'stored ' . min ( (int) $_POST ['price_from'], (int) $_POST ['price_to'] ) . ' to ' . max ( (int) $_POST ['price_from'], (int) $_POST ['price_to'] );

?>
