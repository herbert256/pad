<?php

  foreach ($GLOBALS as $key => $val )
    if ( substr($key, 0, 3) == 'pad' )
      global $$key;

  unset($key);
  unset($val);

  foreach ( $parms as $padK => $padV )
    $$padK = $padV;

  $padRetrieveLevel = $pad;

  include 'level/setup.php'; 

?>