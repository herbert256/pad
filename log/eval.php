<?php

  global $padLogNow;

  $padLogEval = trim($eval);
  if ($value)
    $padLogEval = "$value | $padLogEval";

  $padLogNow ['eval'] [$padLogEval] = $result [$key] [0];

?>