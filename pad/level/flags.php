<?php

  if     ( $pTagResult === NULL )   $pNull = TRUE;
  elseif ( $pTagResult === INF )    $pNull = TRUE;
  elseif ( $pTagResult === NAN )    $pNull = TRUE;
  else                              $pNull = FALSE;

  if     ( is_array($pTagResult) and ! count($pTagResult) ) $pElse = TRUE;
  elseif ( is_array($pTagResult) and   count($pTagResult) ) $pElse = FALSE;
  elseif ( $pTagResult === FALSE                          ) $pElse = TRUE;
  elseif ( $pTagResult === ''                             ) $pElse = TRUE;
  else                                                      $pElse = FALSE;

  if     ( $pNull ) $pHit = FALSE;
  elseif ( $pElse ) $pHit = FALSE;
  else              $pHit = TRUE;

  if     ( $pHit and is_array($pTagResult) ) $pArray = TRUE;
  else                                       $pArray = FALSE;

  if     ( $pHit and $pTagResult !== TRUE and is_scalar($pTagResult) ) $pText = TRUE;
  else                                                                 $pText = FALSE;

  $pHit[$p]  = $pHit;
  $pNull[$p] = $pNull;
  $pElse[$p] = $pElse;
  $pArray[$p]= $pArray;
  $pText[$p] = $pText;
?>