<?php

  if ( strlen($pad_set_name) == 0        ) return pad_error ("{set} syntax error (3)");
  if ( strlen($pad_set_value) == 0       ) return pad_error ("{set} syntax error (4)");
  if ( ! pad_valid_name2 ($pad_set_name) ) return pad_error ("{set} syntax error (5)");

  if ( $pad_tag <> 'set' or $pad_pair )
    if ( isset($GLOBALS [$pad_set_name]) )
      $pad_set_save [$pad_lvl] [$pad_set_name] = $GLOBALS [$pad_set_name];
    else
      $pad_set_delete [$pad_lvl] [] = $pad_set_name;

  $GLOBALS [$pad_set_name] = pad_var_opts ( '', pad_explode($pad_set_value, '|') );

?>