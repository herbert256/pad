<?php

  if ( $pad_walk == 'end' ) { 

    foreach ( $pad_set_save [$pad_lvl] as $pad_k => $pad_v) {
      unset ($GLOBALS [$pad_k]);
      $GLOBALS [$pad_k] = $pad_v;
    }

    foreach ( $pad_set_delete [$pad_lvl] as $pad_k)
      unset ($GLOBALS [$pad_k]);

    return;

  }

  $pad_set_save [$pad_lvl] = $pad_set_delete [$pad_lvl] = [];

  $pad_set_org = pad_explode ($pad_parms, '|');

  foreach ( $pad_set_org as $pad_k => $pad_v ) {

    $pad_w = pad_explode ( $pad_v, '=', 2);

    if ( count($pad_w) <> 2           ) return pad_syntax_error ("{set} syntax error (1)");
    if ( strlen($pad_w[0]) < 2        ) return pad_syntax_error ("{set} syntax error (2)");
    if ( substr($pad_w[0],0,1) <> '$' ) return pad_syntax_error ("{set} syntax error (3)");
    if ( strlen($pad_w[1]) == 0       ) return pad_syntax_error ("{set} syntax error (4)");
    
    $pad_set_var = substr($pad_w[0], 1);
    $pad_set_val = pad_eval ( pad_unescape ( $pad_w[1]??'' ) );

    if ( ! pad_valid_name ($pad_set_var) ) 
      return pad_syntax_error ("{set} syntax error (5)");

    if ( $pad_pair ) {

      $pad_walk = 'end';

      if ( isset($GLOBALS [$pad_set_var]) )
        $pad_set_save_[$pad_lvl] [$pad_set_var] = $GLOBALS [$pad_set_var];
      else
        $pad_set_delete [$pad_lvl] [] = $pad_set_var;

    }

    if ( isset($GLOBALS [$pad_set_var]) )
      unset ($GLOBALS [$pad_set_var]);

    $GLOBALS [$pad_set_var] = $pad_set_val;

  }

  return TRUE;

?>