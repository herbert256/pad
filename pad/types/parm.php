<?php

  if ( isset($pPrmsVal [$p][0]) )
    $pField_tag = $pPrmsVal [$p][0];
  else
    $pField_tag = pFirst_non_parm();

  $pFieldResult = pField_tag ("$pField_tag#$pTag[$p]");

  if ( $pFieldResult === INF )
    return NULL;
  else
    return $pFieldResult;

?>