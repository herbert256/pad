<?php

  if ( $padSeqAction ) {
    $padPrmName  = $padSeqAction;
    $padPrmValue = $padSeqActionParm;
    include 'sequence/actions/action.php';
  }

  foreach ( $padParms [$pad] as $padStartOption ) {

    extract ( $padStartOption );

    if     ( $padPrmKind <> 'option'                                  ) continue;
    elseif ( padSeqDone ( $padPrmName, $padSeqDone )                  ) continue;
    elseif ( $padPrmName == 'action'                                  ) include 'sequence/actions/action.php';
    elseif ( file_exists ( "sequence/actions/types/$padPrmName.php" ) ) include 'sequence/actions/action.php';
    
  }
 
?>