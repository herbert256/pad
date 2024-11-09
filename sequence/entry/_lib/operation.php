<?php

  if ( ! $padSeqEntryParm and file_exists ( "/pad/sequence/types/$padSeqEntryName/flags/operationDouble") ) {
    $padSeqEntryParm = $padParm;
    $padSeqParmUsed  = TRUE;
  }

  $padSeqTypeSave = '';

  include '/pad/sequence/entry/_lib/entry.php';

  $padSeqSeq   = $padSeqEntryName;
  $padPrmValue = $padSeqEntryParm;
  $padSeqType  = 'make';

  include '/pad/sequence/operations/add.php';

  return include '/pad/sequence/sequence.php';
      
?>