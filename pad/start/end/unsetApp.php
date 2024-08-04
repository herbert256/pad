<?php

  foreach ( $GLOBALS as $padStrKey => $padStrVal )
    if ( padValidStore ( $padStrKey ) )
      if ( ! in_array  ( $padStrKey, $padStrApp [$padStrCnt] ) )
        unset ( $GLOBALS [$padStrKey] );

?>