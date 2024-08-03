<?php

  foreach ( $GLOBALS as $padStrKey => $padStrVal )
    if ( padValidStore ($padStrKey) ) 
      if ( ! in_array ( $padStrKey, $padStrClnApp [$padStrCnt] ) )
        unset ( $GLOBALS [$padStrKey] );

?>