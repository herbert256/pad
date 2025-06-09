<?php

  if ( ! $padInfo ) 
    return;

  foreach ( $pqActions as $pqAction => $pqActionParm ) 
    if ( file_exists ( "sequence/actions/single/$pqAction" ) )
      $pqInfo ['actions/single'] [] = $pqAction;
    elseif ( file_exists ( "sequence/actions/double/$pqAction" ) )
      $pqInfo ['actions/double'] [] = $pqAction;

  foreach ( $pqPlays as $pqTmp ) {    
    extract ( $pqTmp );
    $pqInfo ['plays'] [] = "$pqPlay/$pqSeq";
  }

  foreach ( $padParms [$pad] as $padStartOption ) {
    extract ( $padStartOption );
    if ( $padPrmKind == 'option' ) 
      if ( file_exists ( "sequence/options/types/$padPrmName.php") )
        $pqInfo ['options'] [] = $padPrmName;
  }
  
  include 'events/sequence.php';

?>