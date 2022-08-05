<?php

  if     ( $pad_tag_result === NULL )   $pad_null = TRUE;
  elseif ( $pad_tag_result === INF )    $pad_null = TRUE;
  elseif ( $pad_tag_result === NAN )    $pad_null = TRUE;
  else                                  $pad_null = FALSE;

  if     ( is_array($pad_tag_result) and ! count($pad_tag_result) ) $pad_else = TRUE;
  elseif ( is_array($pad_tag_result) and   count($pad_tag_result) ) $pad_else = FALSE;
  elseif ( $pad_tag_result === FALSE                              ) $pad_else = TRUE;
  elseif ( $pad_tag_result === ''                                 ) $pad_else = TRUE;
  else                                                              $pad_else = FALSE;

  if     ( $pad_null ) $pad_hit = FALSE;
  elseif ( $pad_else ) $pad_hit = FALSE;
  else                 $pad_hit = TRUE;

  if     ( $pad_hit and is_array($pad_tag_result) ) $pad_array = TRUE;
  else                                              $pad_array = FALSE;

  if     ( $pad_hit and $pad_tag_result !== TRUE and is_scalar($pad_tag_result) ) $pad_text = TRUE;
  else                                                                            $pad_text = FALSE;

  $pad_parms [$pad] ['hit']   = $pad_hit;
  $pad_parms [$pad] ['null']  = $pad_null;
  $pad_parms [$pad] ['else']  = $pad_else;
  $pad_parms [$pad] ['array'] = $pad_array;
  $pad_parms [$pad] ['text']  = $pad_text;
?>