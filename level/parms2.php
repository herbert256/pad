<?php

  $pad_parms_org = [];
  $pad_parms_tag = [];
  $pad_parms_val = [];

  if ( $pad_tag <> 'set' or $pad_tag_type <> 'tag' ) {

    $pad_parms_org = pad_explode ($pad_parms, ',');
    
    foreach ( $pad_parms_org as $pad_v ) {

      $pad_w = pad_explode ($pad_v, '=', 2);

      if ( pad_valid_name ($pad_w[0] ) )
        if ( count($pad_w) == 1 )
          $pad_parms_tag [$pad_w[0]] = TRUE;
        else
          $pad_parms_tag [$pad_w[0]] = pad_eval ( $pad_w[1] );
      else
          $pad_parms_val [] = pad_eval ( $pad_v );

    }

  }

  $pad_parm = $pad_parms_val [0] ?? '';

  $pad_parameters [$pad_lvl] ['parm']      = $pad_parm;
  $pad_parameters [$pad_lvl] ['parms_org'] = $pad_parms_org;
  $pad_parameters [$pad_lvl] ['parms_tag'] = $pad_parms_tag;  
  $pad_parameters [$pad_lvl] ['parms_val'] = $pad_parms_val;

  if ( $pad_trace and $pad_parms ) {

    $pad_trace_x = "";
    foreach ( $pad_parms_val as $pad_k => $pad_v )
      $pad_trace_x .= "$pad_k=$pad_v "; 
    pad_trace ("parms/val", $pad_trace_x, TRUE);

    $pad_trace_x = "";
    foreach ( $pad_parms_tag as $pad_k => $pad_v )
      $pad_trace_x .= "$pad_k=$pad_v "; 
    pad_trace ("parms/tag", $pad_trace_x, TRUE);
 
  }    

  include PAD_HOME . 'level/check.php';
  include PAD_HOME . 'level/demand.php';

?>