<?php

  $pad_parm      = '';
  
  $pad_parms_app = $pad_parms_pad = $pad_parms_seq = [];
  $pad_parms_org = pad_explode ($pad_parms, '|');

  foreach ( $pad_parms_org as $pad_k => $pad_v )
    $pad_parms_org [$pad_k] = pad_unescape ($pad_v);
  
  foreach ( $pad_parms_org as $pad_k => $pad_v ) {

    $pad_w  = pad_explode ( $pad_v, '=', 2);
    $pad_w1 = $pad_w[0]??'';
    $pad_w2 = $pad_w[1]??'';

    if ( strlen($pad_w1) < 2)
      $pad_f1 = $pad_f2 = '';
    else {
      $pad_f1 = substr($pad_w1, 0, 1);
      $pad_f2 = substr($pad_w1, 1);
    }

    if ( count($pad_w) == 2 and $pad_f1 == '$' and pad_valid_name ($pad_f2) )
      $pad_parms_app [$pad_f2] = pad_eval($pad_w2);
    else if ( count($pad_w) == 2 and $pad_f1 == '@' and pad_valid_name ($pad_f2) )
      $pad_parms_pad [$pad_f2] = pad_eval($pad_w2);
    elseif ( count($pad_w) == 1 and $pad_f1 == '@' and pad_valid_name ($pad_f2) )
      $pad_parms_pad [$pad_f2] = TRUE;
    elseif ( $pad_parm )
      $pad_parms_seq [] = pad_eval($pad_v);
    else {
      $pad_parm = pad_eval($pad_v);
      $pad_parms_seq [] = $pad_parm;
    }

  }

  $pad_parameters [$pad_lvl] ['parm'] = $pad_parm;

  $pad_parameters [$pad_lvl] ['parms_org'] = $pad_parms_org;
  $pad_parameters [$pad_lvl] ['parms_app'] = $pad_parms_app;
  $pad_parameters [$pad_lvl] ['parms_pad'] = $pad_parms_pad;
  $pad_parameters [$pad_lvl] ['parms_seq'] = $pad_parms_seq;

  if ( $pad_trace and $pad_parms ) {
    $pad_trace = "";
    foreach ( $pad_parms_seq as $pad_k => $pad_v )
      if ( is_scalar($pad_v))
        $pad_trace .= " $pad_k=$pad_v"; 
      else
        $pad_trace .= " $pad_k=*ARRAY*";
    foreach ( $pad_parms_pad as $pad_k => $pad_v )
      if ( is_scalar($pad_v))
        $pad_trace .= " $pad_k=$pad_v";
      else
        $pad_trace .= " $pad_k=*ARRAY*";
    foreach ( $pad_parms_app as $pad_k => $pad_v )
      if ( is_scalar($pad_v))
        $pad_trace .= " $pad_k=$pad_v";
      else
        $pad_trace .= " $pad_k=*ARRAY*";
    pad_trace ("tag/parms", $pad_trace, TRUE);
  }    

?>