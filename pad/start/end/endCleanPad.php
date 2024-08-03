<?php

  foreach ( $GLOBALS as $padStrKey => $padStrVal )
    if ( str_starts_with ( $padStrKey, 'pad' ) )
      if ( ! str_starts_with ( $padStrKey, 'padStr' ) )
        if ( ! in_array  ( $padStrKey, $padStrClnPad [$padStrCnt] ) )
          unset ( $GLOBALS [$padStrKey] );

?>