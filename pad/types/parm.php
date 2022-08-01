<?php

  $pad_field_tag = $pad_parms_val[0] ?? '-1';

  $pad_parm_result = pad_field_tag ("$pad_field_tag#$pad_tag");

  if ( $pad_parm_result === INF )
    return NULL;
  else
    return $pad_parm_result;

?>