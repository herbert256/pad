<?php


  if ( $padSeqType == 'keep'   or $padSeqPrefix == 'keep'   or $padSeqTag == 'keep'   ) $padSeqKeep   = TRUE;
  if ( $padSeqType == 'remove' or $padSeqPrefix == 'remove' or $padSeqTag == 'remove' ) $padSeqRemove = TRUE;
  if ( $padSeqType == 'flag'   or $padSeqPrefix == 'flag'   or $padSeqTag == 'flag'   ) $padSeqFlag   = TRUE;

  include 'sequence/inits/check/tag.php';

  $padSeqCheck = 'keep';   include "sequence/inits/check/go.php";
  $padSeqCheck = 'remove'; include "sequence/inits/check/go.php";
  $padSeqCheck = 'flag';   include "sequence/inits/check/go.php";

?>