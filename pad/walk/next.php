<?php
     
  if ( padXref )
    include pad . 'info/types/xref/events/walk.php';

  $padWalk [$pad] = 'next';
  
  include pad . "level/go.php"; 
  include pad . "level/flags.php";

  if ( $padWalk [$pad] ) {

    if ( $padArray [$pad] ) 
      $padData [$pad] = $padTagResult;
 
    reset ( $padData [$pad] );

  }

?>