<?php

  global $padStrClnPad;

  $padStrClnPad [$padStrCnt] = [];

  foreach ( $GLOBALS as $padStrKey => $padStrVal )
    if ( str_starts_with ( $padStrKey, 'pad' ) )
      if ( ! str_starts_with ( $padStrKey, 'padStr' ) )
        $padStrClnPad [$padStrCnt] [$padStrKey] = TRUE;

?>