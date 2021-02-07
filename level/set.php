<?php

  if ( strlen($pad_set_name) == 0        ) return pad_error ("{set} syntax error (3)");
  if ( strlen($pad_set_value ) == 0      ) return pad_error ("{set} syntax error (4)");
  if ( ! pad_valid_name2 ($pad_set_name) ) return pad_error ("{set} syntax error (5)");

  $pad_between_save = $pad_between;
  $pad_between = '!' . $pad_set_name . ' ' . $pad_set_value;
  $pad_set_set [$pad_lvl] [$pad_set_name] = include PAD_HOME . 'level/var.php';
  $pad_between = $pad_between_save;

  if ( $pad_tag <> 'set' or $pad_pair ) {

    if ( isset($GLOBALS [$pad_set_name]) )
      $pad_set_save [$pad_lvl] [$pad_set_name] = $GLOBALS [$pad_set_name];
    else
      $pad_set_delete [$pad_lvl] [] = $pad_set_name;

  }

  $GLOBALS [$pad_set_name] = $pad_set_set [$pad_lvl] [$pad_set_name];

?>