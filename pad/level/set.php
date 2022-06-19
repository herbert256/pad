<?php

  if ( strlen($pad_set_name) == 0                ) return pad_error ("{set} syntax error (1)");
  if ( strlen($pad_set_value) == 0               ) return pad_error ("{set} syntax error (2)");
  if ( ! preg_match('/^[A-Za-z0-9_]+$/', $name ) ) return pad_error ("{set} syntax error (3)");
  if ( ! ctype_alpha(substr($name, 0, 1))        ) return pad_error ("{set} syntax error (4)");

  if ( $pad_tag <> 'set' or $pad_pair )
    if ( isset($GLOBALS [$pad_set_name]) )
      $pad_set_save [$pad_lvl] [$pad_set_name] = $GLOBALS [$pad_set_name];
    else
      $pad_set_delete [$pad_lvl] [] = $pad_set_name;

  $GLOBALS [$pad_set_name] = pad_var_opts ( '', pad_explode($pad_set_value, '|') );

  return TRUE;
  
?>