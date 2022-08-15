<?php

  // Callback mode: before

  pCallback_before_xxx ('init');

  foreach ( $padData [$pad] as $padK => $padV)
    pCallback_before_row ( $padData [$pad] [$padK] );

  pCallback_before_xxx ('exit'); 

?>