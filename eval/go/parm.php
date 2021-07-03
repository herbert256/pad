<?php
 
  if ($pad_parm_value)
    $pad_parm_tag = $pad_parm_value;
  elseif ( isset ( $pad_parm_parms [0] ) )
    $pad_parm_tag = $pad_parm_parms [0];
  else 
    $pad_parm_tag  = -1;

  if ( $pad_parm_value and isset ( $pad_parm_parms [0] ) )
    $pad_parm_parm = $pad_parm_parms [0];
  elseif ( isset ( $pad_parm_parms [1] ) and ! $pad_parm_value )
    $pad_parm_parm = $pad_parm_parms [1];
  else
    $pad_parm_parm = '';      

  $pad_parm_result = pad_field_tag ("$pad_parm_tag#$pad_parm_field#$pad_parm_parm");

  if ( $GLOBALS['pad_trace'] )
    pad_build_reference ("eval/go/parm/$pad_parm_tag");

  pad_trace ("parm", "$pad_parm_tag#$pad_parm_field#$pad_parm_parm --> " . pad_info ( $pad_parm_result ) );

  if ( $pad_parm_result === PAD_NOT_FOUND )
    return NULĹ;
  else
    return $pad_parm_result;

?>