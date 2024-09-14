<?php

  include '/pad/sequence/entry/inits/inits.php';

  if ( $padTag [$pad] == 'element' and is_numeric ($padParm) ) {
    $padSeqEntryParm = $padOpt [2] ?? '';
    $padPrmValue ='element|' . $padParm;
  } else {
    $padSeqEntryParm = $padParm;
    $padPrmValue = $padTag [$pad];
  }

  $padSeqOptions [] = [ 
    'padPrmName'  => 'one',
    'padPrmValue' => $padPrmValue
  ];

  $padSeqEntryList = $padPrm [$pad];

  return include '/pad/sequence/sequence.php';

?>