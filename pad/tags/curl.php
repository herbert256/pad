<?php

  // {curl 'http://...'} fetches a URL and hands the response body back as the tag's
  // value.
  //
  // url= or the first parameter gives the address; every variable {set} at this level is
  // appended to it as a query parameter, and a SELF:// prefix becomes $padHost so an app
  // can call itself whatever host and mount prefix it is served under. The tag's
  // parameters as a whole are the option array for padCurl(), so method=, data=, headers
  // and the rest travel with it. Anything but HTTP 200 raises a PAD error.
  //
  // ttl=600 keeps the answer that many seconds (lib/curlCache.php): the requests in that
  // time are answered from the copy, and when the source fails afterwards the last good
  // copy is served and the failure logged. The ttl is the cache's, not padCurl's, so it
  // is no part of what is fetched.

  if ( ! isset ($padPrm [$pad] ['url']) )
    $padPrm [$pad] ['url'] = $padParm;

  foreach ( $padSetLvl [$pad] as $padK => $padV )
    $padPrm [$pad] ['url'] = padAddGet ($padPrm [$pad] ['url'], $padK, $padV );

  $padPrm [$pad] ['url'] = str_replace('SELF://', $padHost, $padPrm [$pad] ['url']);

  $padCurlTtl = padTagParm ( 'ttl', 0 );
  $padCurlIn  = array_diff_key ( $padPrm [$pad], [ 'ttl' => 1 ] );

  $padCurl = ( $padCurlTtl ) ? padCurlCached ( $padCurlIn, $padCurlTtl ) : padCurl ( $padCurlIn );

  if ( $padCurl ['result'] != '200' )
    padError ( "Curl failed: " . $padCurl ['result'] . ' ' . $padCurl ['url'] );

  return $padCurl ['data'];

?>
