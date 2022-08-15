<?php

  if ( ! count ($padarm) and $value )
    $padarm [0] = $value;

  return call_user_func_array ($name, $padarm);

?>