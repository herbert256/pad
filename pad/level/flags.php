<?php

  if     ( $pTagResult [$p] === NULL )   $pNull [$p] = TRUE;
  elseif ( $pTagResult [$p] === INF )    $pNull [$p] = TRUE;
  elseif ( $pTagResult [$p] === NAN )    $pNull [$p] = TRUE;
  else                              $pNull [$p] = FALSE;

  if     ( is_array($pTagResult [$p]) and ! count($pTagResult [$p]) ) $pElse [$p] = TRUE;
  elseif ( is_array($pTagResult [$p]) and   count($pTagResult [$p]) ) $pElse [$p] = FALSE;
  elseif ( $pTagResult [$p] === FALSE                          ) $pElse [$p] = TRUE;
  elseif ( $pTagResult [$p] === ''                             ) $pElse [$p] = TRUE;
  else                                                      $pElse [$p] = FALSE;

  if     ( $pNull [$p] ) $pHit [$p] = FALSE;
  elseif ( $pElse [$p] ) $pHit [$p] = FALSE;
  else                   $pHit [$p] = TRUE;

  if     ( $pHit [$p] and is_array($pTagResult [$p]) ) $pArray [$p] = TRUE;
  else                                            $pArray [$p] = FALSE;

  if     ( $pHit [$p] and $pTagResult [$p] !== TRUE and is_scalar($pTagResult [$p]) ) $pText [$p] = TRUE;
  else                                                                      $pText [$p] = FALSE;


?>