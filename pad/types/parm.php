<?php

  if ( isset($pad_prms_val[0]) )
    $pad_field_tag = $pad_prms_val[0];
  else
    $pad_field_tag = pad_first_non_parm();

  $pad_parm_result = pad_field_tag ("$pad_field_tag#$pad_tag");

  if ( $pad_parm_result === INF )
    return NULL;
  else
    return $pad_parm_result;

?>