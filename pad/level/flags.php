<?php

  if     ( $pTag_result === NULL )   $pNull = TRUE;
  elseif ( $pTag_result === INF )    $pNull = TRUE;
  elseif ( $pTag_result === NAN )    $pNull = TRUE;
  else                                  $pNull = FALSE;

  if     ( is_array($pTag_result) and ! count($pTag_result) ) $pElse = TRUE;
  elseif ( is_array($pTag_result) and   count($pTag_result) ) $pElse = FALSE;
  elseif ( $pTag_result === FALSE                              ) $pElse = TRUE;
  elseif ( $pTag_result === ''                                 ) $pElse = TRUE;
  else                                                              $pElse = FALSE;

  if     ( $pNull ) $pHit = FALSE;
  elseif ( $pElse ) $pHit = FALSE;
  else                 $pHit = TRUE;

  if     ( $pHit and is_array($pTag_result) ) $pArray = TRUE;
  else                                              $pArray = FALSE;

  if     ( $pHit and $pTag_result !== TRUE and is_scalar($pTag_result) ) $pText = TRUE;
  else                                                                            $pText = FALSE;

  $pParms [$p] ['hit']   = $pHit;
  $pParms [$p] ['null']  = $pNull;
  $pParms [$p] ['else']  = $pElse;
  $pParms [$p] ['array'] = $pArray;
  $pParms [$p] ['text']  = $pText;
?>