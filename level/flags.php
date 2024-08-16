<?php

  if     ( $padTagResult === NULL ) $padNull [$pad] = TRUE;
  elseif ( $padTagResult === INF  ) $padNull [$pad] = TRUE;
  elseif ( $padTagResult === NAN  ) $padNull [$pad] = TRUE;
  else                              $padNull [$pad] = FALSE;

  if     ( is_array($padTagResult) and ! count($padTagResult) ) $padElse [$pad] = TRUE;
  elseif ( is_array($padTagResult) and   count($padTagResult) ) $padElse [$pad] = FALSE;
  elseif ( $padTagResult === FALSE                            ) $padElse [$pad] = TRUE;
  elseif ( $padTagResult === ''                               ) $padElse [$pad] = TRUE;
  else                                                          $padElse [$pad] = FALSE;

  if     ( $padNull [$pad] ) $padHit [$pad] = FALSE;
  elseif ( $padElse [$pad] ) $padHit [$pad] = FALSE;
  else                       $padHit [$pad] = TRUE;

  if     ( $padHit [$pad] and is_array($padTagResult) ) $padArray [$pad] = TRUE;
  else                                                  $padArray [$pad] = FALSE;

?>