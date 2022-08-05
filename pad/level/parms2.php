<?php

  $pad_prms_tag = [];
  $pad_prms_val = [];

  if ( ! in_array ( $pad_tag??'', ['if', 'case', 'while', 'until'] )  ) {
   
    $pad_prms_org = pad_explode ($pad_prms??'', ',');
    
    foreach ( $pad_prms_org as $pad_v ) {

      if ( $pad_v == 'trace' )
        include 'trace/option.php';

      $pad_w = pad_explode ($pad_v, '=', 2);

      if ( count($pad_w) == 2 and substr($pad_w[0], 0, 1) == '$') {
        $pad_set_name  = trim(substr($pad_w[0], 1));
        $pad_set_value = $pad_w[1];
        include PAD . 'level/set.php';
        continue;
      } 

      if ( pad_valid ($pad_w[0]) and ! is_numeric($pad_w[0]) )
        if ( count($pad_w) == 1 )
          $pad_prms_tag [$pad_w[0]] = TRUE;
        else
          $pad_prms_tag [$pad_w[0]] = pad_eval ( $pad_w[1] );
      else
        $pad_prms_val [] = pad_eval ( $pad_v );

    }
 
  }
 
?>