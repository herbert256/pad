<?php

  if (strlen($value) > $parm[0])
    return substr($value, 0, $parm[0]);
  else
    return $value;

?>