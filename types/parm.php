<?php

  if ( isset($pad_parms_val[0] )
    $pad_field_tag = $pad_parms_val[0];
  else
    $pad_field_tag = '-1';

  $pad_parm_result = pad_field_tag ("pad_field_tag#$pad_tag");

  if ( $pad_parm_result === PAD_NOT_FOUND )
    return NULL;
  else
    return $pad_parm_result;

?>