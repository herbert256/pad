<?php

  $pqMake   = $padPrm [$pad] ['make']     ?? '';
  $pqFlag   = $padPrm [$pad] ['flag']     ?? '';
  $pqKeep   = $padPrm [$pad] ['keep']     ?? '';
  $pqRemove = $padPrm [$pad] ['remove']   ?? '';
  
  if ( $pqType == 'make'   or $pqPrefix == 'make'   or $pqTag == 'make'   ) $pqMake   = TRUE;
  if ( $pqType == 'keep'   or $pqPrefix == 'keep'   or $pqTag == 'keep'   ) $pqKeep   = TRUE;
  if ( $pqType == 'remove' or $pqPrefix == 'remove' or $pqTag == 'remove' ) $pqRemove = TRUE;
  if ( $pqType == 'flag'   or $pqPrefix == 'flag'   or $pqTag == 'flag'   ) $pqFlag   = TRUE;

  $pqCheck = 'make';   include "sequence/inits/check/go.php";
  $pqCheck = 'keep';   include "sequence/inits/check/go.php";
  $pqCheck = 'remove'; include "sequence/inits/check/go.php";
  $pqCheck = 'flag';   include "sequence/inits/check/go.php";

?>