<?php

  if ( isset($pPrms_val[0]) )
    $pField_tag = $pPrms_val[0];
  else
    $pField_tag = pFirst_non_parm();

  $pParm_result = pField_tag ("$pField_tag#$pTag");

  if ( $pParm_result === INF )
    return NULL;
  else
    return $pParm_result;

?>