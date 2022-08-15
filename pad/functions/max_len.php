<?php

  if (strlen($value) > $padarm[0])
    return substr($value, 0, $padarm[0]);
  else
    return $value;

?>
