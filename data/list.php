<?php

  $work = padExplode(substr($data, 1, -1), ',');

  foreach ($work as $key => $value)
    $result [$key] = padEval($value);

  return $result;

?>