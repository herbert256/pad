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

  if     ( $pad_null ) $pad_true = FALSE;
  elseif ( $pad_else ) $pad_true = FALSE;
  else                 $pad_true = TRUE;

  if     ( $pad_true and is_array($pad_tag_result) ) $pad_array = TRUE;
  else                                               $pad_array = FALSE;

  if     ( $pad_true and ! is_array($pad_tag_result) ) $pad_text = TRUE;
  else                                                 $pad_text = FALSE;

?>