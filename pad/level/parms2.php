<?php

  $pad_parms_org = [];
  $pad_parms_seq = [];
  $pad_parms_tag = [];
  $pad_parms_val = [];

  $pad_parms_org = pad_explode ($pad_parms, ',');
  
  foreach ( $pad_parms_org as $pad_v ) {

    $pad_v = str_replace('&comma;', ',', $pad_v);

    $pad_w = pad_explode ($pad_v, '=', 2);

    if ( count($pad_w) == 2 )
      $pad_w[1] = str_replace('&is;', '=', $pad_w[1]);


    if ( count($pad_w) == 2 and substr($pad_w[0], 0, 1) == '$') {

      $pad_set_name  = trim(substr($pad_w[0], 1));
      $pad_set_value = $pad_w[1];
      include PAD . 'level/set.php';

      $pad_parms_seq [] = $GLOBALS [$pad_set_name];

    } else {

      if ( pad_valid ($pad_w[0]) and ! is_numeric($pad_w[0]) )
        if ( count($pad_w) == 1 ) {
          $pad_parms_tag [$pad_w[0]] = TRUE;
          $pad_parms_seq [] = TRUE;
        }
        else {
          $pad_parms_tag [$pad_w[0]] = pad_eval ( $pad_w[1] );
          $pad_parms_seq [] = $pad_parms_tag [$pad_w[0]];
        }
      else {
        $pad_parms_val [] = pad_eval ( $pad_v );
        $pad_parms_seq [] = end ($pad_parms_val);
      }

    }

  }

  $pad_parm = $pad_parms_val [0] ?? '';

  $pad_parameters [$pad_lvl] ['parm']      = $pad_parm;
  $pad_parameters [$pad_lvl] ['parms_org'] = $pad_parms_org;
  $pad_parameters [$pad_lvl] ['parms_seq'] = $pad_parms_seq;
  $pad_parameters [$pad_lvl] ['parms_tag'] = $pad_parms_tag;  
  $pad_parameters [$pad_lvl] ['parms_val'] = $pad_parms_val;

  include PAD . 'level/check.php';

?>