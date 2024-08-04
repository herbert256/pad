<?php

  foreach ( $GLOBALS as $padStrKey => $padStrVal )
    if ( padStrPad ( $padStrKey ) )
      if ( ! in_array  ( $padStrKey, $padStrZZZ [$padStrCnt] ) )
        unset ( $GLOBALS [$padStrKey] );

?>