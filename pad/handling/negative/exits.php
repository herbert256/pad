<?php

  // Second half of the negative option: replaces the tag's data with everything the
  // handler did NOT select.
  //
  // The keys left in $padData [$pad] are compared with the copy negative/inits.php put in
  // $padHandOld; the rows that are missing are kept and their 'x' key prefix is stripped
  // again, so {items first="3" negative} yields every item but the first three.

  $padHandKeysNew = array_keys ( $padData [$pad] );
  $padHandKeysOld = array_keys ( $padHandOld     );

  $padData [$pad] = [];

  foreach ( $padHandKeysOld as $padHandOldKey )
    if ( ! in_array ( $padHandOldKey, $padHandKeysNew ) )
      $padData [$pad] [  substr ( $padHandOldKey, 1 ) ] = $padHandOld [$padHandOldKey];

?>