<?php

  if ( isset($pPrmsVal[$p][0]) )
    $pField_tag = $pPrmsVal[$p][0];
  else
    $pField_tag = pFirst_non_parm();

  $pPrm[$p]_result = pField_tag ("$pField_tag#$pTag");

  if ( $pPrm[$p]_result === INF )
    return NULL;
  else
    return $pPrm[$p]_result;

?>