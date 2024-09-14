<?php

  include '/pad/sequence/entry/inits/inits.php';

  $padSeqEntryParm = '';
  $padSeqEntryList = [ $padSeqEntryFirst => TRUE ];
  
  $padSeqOptions [] = [ 
    'padPrmName'  => $padTag [$pad],
    'padPrmValue' => $padSeqEntryRest
  ];

  return include '/pad/sequence/sequence.php';

?>