<?php
  
  $padPagTyp = padTagParm ( 'type', 'sandbox' );
  
  if ( padTagParm ('optional') ) {

    $padPagTarget = padTagParm ( 'page', $padOpt [1] );
    $padPagTarget = padPageGetName ();

    if ( ! $padPagTarget )
      return NULL;

  }
  
  return include pad . "page/$padPagTyp.php";
   
?>