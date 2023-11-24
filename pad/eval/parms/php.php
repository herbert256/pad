<?php

  if ( ! count ($parm) and $value )
    $parm [0] = $value;

  return call_user_func_array ($name, $parm);

?>