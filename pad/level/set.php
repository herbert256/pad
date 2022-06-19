<?php

  if ( ! pad_valid ($pad_set_name) )
    return pad_error ("{set} syntax error (1)");

  if ( $pad_tag <> 'set' or $pad_pair )
    if ( isset($GLOBALS [$pad_set_name]) )
      $pad_set_save [$pad_lvl] [$pad_set_name] = $GLOBALS [$pad_set_name];
    else
      $pad_set_delete [$pad_lvl] [] = $pad_set_name;

  $GLOBALS [$pad_set_name] = pad_var_opts ( '', pad_explode($pad_set_value, '|') );

  return TRUE;
  
?>