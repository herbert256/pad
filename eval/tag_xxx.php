<?php
  
  $pad_lvl = $GLOBALS['pad_lvl'];

  $pad_content = $value;
  $pad_false   = '';
  $pad_pair    = FALSE;

  $pad_between = $name . ' ' . implode ( ' | ' , $parm );
  include PAD_HOME . 'level/parms1.php';

  $pad_parms_org = $parm;
  $pad_parms_seq = $parm;
  $pad_parms_tag = [];
  $pad_parms_val = $parm;

  $pad_from_eval = TRUE;  
  include PAD_HOME . 'level/start.php';

  $pad_lvl--;

  foreach ( $pad_parameters [$pad_lvl] as $pad_k => $pad_v )
    $GLOBALS['pad_'.$pad_k] = $pad_v;

  if     ( is_array($pad_tag_result)       ) return $pad_tag_result;
  elseif ( $pad_tag_result === FALSE       ) return '';
  elseif ( $pad_tag_result === TRUE        ) return '1'
  elseif ( ! count ($pad_data [$pad_lvl+1) ) return '';
  else                                       return $pad_base [$pad_lvl+1];

?>