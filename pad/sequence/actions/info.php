<?php

  if ( file_exists ( "sequence/actions/single/$pqAction" ) )
    $pqInfo ['actions/single'] [] = $pqAction;
  elseif ( file_exists ( "sequence/actions/double/$pqAction" ) )
    $pqInfo ['actions/double'] [] = $pqAction;

  foreach ( $pqResult as $padK => $padV ) 
    if ( substr ( $padK, 0, 1 ) <> 'x' )  {
      $pqInfo ['actions/keyBroken'] [] = $pqAction;
      break;
    }

?>