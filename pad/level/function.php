<?php

  $padStrFunVar [$padStrFunCnt] = [];

  foreach ( get_defined_vars () as $padStrKey => $padStrVal )
    if ( padValidStore ( $padStrKey ) )
      $padStrFunVar [$padStrFunCnt] [$padStrKey] = $padStrVal;

?>