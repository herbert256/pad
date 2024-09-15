<?php

  include '/pad/sequence/entry/_lib/entry.php';

  $padSeqOptions [] = [ 
    'padPrmName'  => $padSeqEntryName,
    'padPrmValue' => $padSeqEntryParm
  ];

  return include '/pad/sequence/sequence.php';

?>