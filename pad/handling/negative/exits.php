<?php

  $padHandKeysNew = array_keys ( $padData [$pad] );
  $padHandKeysOld = array_keys ( $padHandOld     );

  $padData [$pad] = [];

  foreach ( $padHandKeysOld as $padHandOldKey )
    if ( ! in_array ( $padHandOldKey, $padHandKeysNew ) )
      $padData [$pad] [  substr ( $padHandOldKey, 1 ) ] = $padHandOld [$padHandOldKey];

?>
