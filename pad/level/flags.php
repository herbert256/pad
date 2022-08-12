<?php

  if     ( $pTagResult === NULL )   $pNull [$p] = TRUE;
  elseif ( $pTagResult === INF )    $pNull [$p] = TRUE;
  elseif ( $pTagResult === NAN )    $pNull [$p] = TRUE;
  else                                   $pNull [$p] = FALSE;

  if     ( is_array($pTagResult) and ! count($pTagResult) ) $pElse [$p] = TRUE;
  elseif ( is_array($pTagResult) and   count($pTagResult) ) $pElse [$p] = FALSE;
  elseif ( $pTagResult === FALSE                          )      $pElse [$p] = TRUE;
  elseif ( $pTagResult === ''                             )      $pElse [$p] = TRUE;
  else                                                                $pElse [$p] = FALSE;

  if     ( $pNull [$p] ) $pHit [$p] = FALSE;
  elseif ( $pElse [$p] ) $pHit [$p] = FALSE;
  else                   $pHit [$p] = TRUE;

  if     ( $pHit [$p] and is_array($pTagResult) ) $pArray [$p] = TRUE;
  else                                                 $pArray [$p] = FALSE;

  if     ( $pHit [$p] and $pTagResult !== TRUE and is_scalar($pTagResult) ) $pText [$p] = TRUE;
  else                                                                                $pText [$p] = FALSE;


?>