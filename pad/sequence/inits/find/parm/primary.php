<?php

  if ( $pqFindParm and $pqSeq and ! $pqAction ) {
    $pqParm     = $pqFindParm;
    $pqFindParm = '';
  }

  if ( $pqFindParm and ! $pqSeq and $pqAction ) {
    $pqActionParm = $pqFindParm;
    $pqFindParm   = '';
  }

?>
