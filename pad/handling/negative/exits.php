<?php

  // Second half of the negative option: replaces the tag's data with everything the
  // handler did NOT select.
  //
  // The keys left in $padData [$pad] are compared with the copy negative/inits.php put in
  // $padHandOld; the rows that are missing are kept and their 'x' key prefix is stripped
  // again, so {items first="3" negative} yields every item but the first three.

  // The selected keys as a set, looked up with isset: in_array walked the whole kept list
  // for every row of the original, so negative over a few thousand rows - {xs where='...',
  // negative} - cost n^2 and ran for seconds. The order of $padHandOld is kept by walking
  // it, as array_keys did.

  $padHandKeysNew = array_flip ( array_keys ( $padData [$pad] ) );

  $padData [$pad] = [];

  foreach ( $padHandOld as $padHandOldKey => $padHandOldVal )
    if ( ! isset ( $padHandKeysNew [$padHandOldKey] ) )
      $padData [$pad] [  substr ( $padHandOldKey, 1 ) ] = $padHandOldVal;

?>
