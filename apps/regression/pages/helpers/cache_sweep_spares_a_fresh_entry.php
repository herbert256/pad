<?php

  // The sweep of expired entries and the requests that put entries, at the same time: a
  // sweep that read an entry as expired removed whatever stood at its path a moment later -
  // a value put fresh in between was gone, a padCacheHas right after its own put answered
  // FALSE - and a put that found the old entry there and had it removed under it ended on
  // fileperms (). The sweep now removes an entry only when the path still holds the file it
  // read, and under the application's lock, which every write takes shared.

  $sweepUrl  = $padGoExt . 'helpers/cache_sweep_spares_a_fresh_entry_';
  $sweepGot  = padCurlMulti ( [ 'sweep 1' => $sweepUrl . 'sweep&padInclude', 'put 1' => $sweepUrl . 'put&set=b&padInclude',
                                'sweep 2' => $sweepUrl . 'sweep&padInclude', 'put 2' => $sweepUrl . 'put&set=c&padInclude',
                                'sweep 3' => $sweepUrl . 'sweep&padInclude' ] );

  ksort ( $sweepGot );

  $sweepResult = [];

  foreach ( $sweepGot as $sweepWhat => $sweepOne )
    $sweepResult [] = "$sweepWhat: " . $sweepOne ['result'] . ' ' . trim ( substr ( $sweepOne ['data'], 0, 40 ) );

  $sweepResult = implode ( "\n", $sweepResult );

?>
