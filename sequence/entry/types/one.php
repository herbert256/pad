<?php

  $padSeqEntryName = $padTag [$pad];
  $padSeqEntryParm = $padOpt [$pad] [1]; 

  include '/pad/sequence/entry/_lib/entry.php';

  $padSeqOptions [] = [ 
    'padPrmName'  => 'one',
    'padPrmValue' => "$padSeqEntryName|$padSeqEntryParm"
  ]; 

  return include '/pad/sequence/sequence.php';
  
?>