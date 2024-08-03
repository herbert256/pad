<?php

  foreach ( $GLOBALS as $padStrKey => $padStrVal )
    if ( padStrClnFld ( $padStrKey ) )
      if ( ! in_array  ( $padStrKey, $padStrClnPad [$padStrCnt] ) )
        unset ( $GLOBALS [$padStrKey] );
      else
        $GLOBALS [$padStrKey] = $padStrVal;

?>