<?php

  $padSeqSeqSave = $padSeqSeq;
  
  foreach ( $padOptionsMulti as $padStartOption ) {

    extract ( $padStartOption );

    if ( in_array ( $padPrmName, $padSeqDone ) )
      continue;

    if     ( $padPrmName == 'store'  )                                  continue;
    elseif ( str_starts_with ( $padPrmName, 'store'  ) )                include 'sequence/store/startWith.php';  
    elseif ( str_starts_with ( $padPrmName, 'action' ) )                include 'sequence/actions/action.php';
    elseif ( file_exists ( "sequence/actions/types/$padPrmName.php" ) ) include 'sequence/actions/action.php';
    
  }
 
  $padSeqSeq = $padSeqSeqSave;

?>