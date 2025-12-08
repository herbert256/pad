<?php

  if ( ! $padInfo ) 
    return;

  foreach ( $pqActions as $pqAction => $pqActionParm ) 
    if ( file_exists ( PQ . "actions/single/$pqAction" ) )
      $pqInfo ['actions/single'] [] = $pqAction;
    elseif ( file_exists ( PQ . "actions/double/$pqAction" ) )
      $pqInfo ['actions/double'] [] = $pqAction;

  foreach ( $pqPlays as $pqTmp ) {    
    extract ( $pqTmp );
    $pqInfo ['plays'] [] = "$pqPlay/$pqSeq";
  }

  foreach ( $padParms [$pad] as $padStartOption ) {
    extract ( $padStartOption );
    if ( $padPrmKind == 'option' ) 
      if ( file_exists ( PQ . "options/types/$padPrmName.php") )
        $pqInfo ['options'] [] = $padPrmName;
  }
    
  $pqInfo ['start'] = basename ( str_replace ( PQ . 'start/', '', $pqSartScript ), '.php' );

  include PAD . 'events/sequence.php';

?>