<?php

  $padSeqSeqSave = $padSeqSeq;
  
  foreach ( $padSeqOptions as $padSeqOption ) {

    extract ( $padSeqOption );

    if     ( $padPrmName == 'one'    )                                  include 'sequence/one/one.php';
    elseif ( $padPrmName == 'action' )                                  include 'sequence/actions/action.php';
    elseif ( $padPrmName == 'store'  )                                  continue;
    elseif ( str_starts_with ( $padPrmName, 'action' ) )                include 'sequence/actions/startWith.php';
    elseif ( str_starts_with ( $padPrmName, 'store'  ) )                include 'sequence/store/startWith.php';  
    elseif ( file_exists ( "sequence/actions/types/$padPrmName.php" ) ) include 'sequence/actions/after.php';
    elseif ( file_exists ( "sequence/one/types/$padPrmName.php" ) )     include 'sequence/one/one.php';
    
  }
 
  $padSeqSeq = $padSeqSeqSave;

?>