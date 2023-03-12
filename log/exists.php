<?php

  global $padLogNow, $padLogExists;

  if ( ! $padLogExists )
    return;

  $padLogNow ['exists'] [] [$file] = " - $return";

?>