<?php

  $pad_parms_org = [];
  $pad_parms_tag = [];
  $pad_parms_val = [];

  if ( $pad_tag == 'if' )
    return;

  $pad_parms_org = pad_explode ($pad_parms, ',');
  
  foreach ( $pad_parms_org as $pad_v ) {

    if ( $pad_v == 'trace' )
      include 'trace/option.php';

    $pad_v = str_replace ('&comma;', ',', $pad_v);
    $pad_w = pad_explode ($pad_v, '=', 2);

    if ( count($pad_w) == 2 and substr($pad_w[0], 0, 1) == '$') {
      $pad_set_name  = trim(substr($pad_w[0], 1));
      $pad_set_value = $pad_w[1];
      include PAD . 'level/set.php';
      continue;
    } 

    if ( pad_valid ($pad_w[0]) and ! is_numeric($pad_w[0]) )
      if ( count($pad_w) == 1 )
        $pad_parms_tag [$pad_w[0]] = TRUE;
      else
        $pad_parms_tag [$pad_w[0]] = pad_eval ( $pad_w[1] );
    else
      $pad_parms_val [] = pad_eval ( $pad_v );

  }

  $pad_parm = $pad_parms_val [0] ?? '';

  $pad_parameters [$pad_lvl] ['parm']      = $pad_parm;
  $pad_parameters [$pad_lvl] ['parms_org'] = $pad_parms_org;
  $pad_parameters [$pad_lvl] ['parms_tag'] = $pad_parms_tag;  
  $pad_parameters [$pad_lvl] ['parms_val'] = $pad_parms_val;

  include PAD . 'level/check.php';
  
?>