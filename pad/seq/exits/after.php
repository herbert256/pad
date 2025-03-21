<?php

  $padSeqSeqSave = $padSeqSeq;
  
  foreach ( $padSeqOptions as $padSeqOption ) {

    extract ( $padSeqOption );

    if     ( $padPrmName == 'one'    )                             include 'seq/one/one.php';
    elseif ( $padPrmName == 'action' )                             include 'seq/actions/action.php';
    elseif ( $padPrmName == 'store'  )                             continue;
    elseif ( str_starts_with ( $padPrmName, 'action' ) )           include 'seq/actions/startWith.php';
    elseif ( str_starts_with ( $padPrmName, 'store'  ) )           include 'seq/store/startWith.php';  
    elseif ( file_exists ( "seq/options/types/$padPrmName.php" ) ) continue;
    elseif ( file_exists ( "seq/actions/types/$padPrmName.php" ) ) include 'seq/actions/after.php';
    elseif ( file_exists ( "seq/one/types/$padPrmName.php" ) )     include 'seq/one/one.php';
    
  }
 
  $padSeqSeq = $padSeqSeqSave;

?>