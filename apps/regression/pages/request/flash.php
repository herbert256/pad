<?php

  // A flash message survives exactly one redirect: the fixture flashes and redirects, and
  // the page the redirect leads to - fetched with the cookies the redirect set, as a
  // browser would - shows it; the same page asked again, even with the sign cookie still
  // sent, has nothing to show. The demos passed ?sent=1 in the url instead.

  $flashOne = padCurl ( [ 'url' => $padGoExt . 'request/flasher&padInclude', 'options' => [ 'FOLLOWLOCATION' => FALSE ] ] );

  $flashJar = [ 'PHPSESSID' => $flashOne ['cookies'] ['PHPSESSID'] ?? '',
                'padFlash'  => $flashOne ['cookies'] ['padFlash']  ?? '' ];

  $flashUrl = ( $flashOne ['info'] ['redirect_url'] ?? '' ) . '&padInclude';

  $flashTwo   = padCurl ( [ 'url' => $flashUrl, 'cookies' => $flashJar ] );
  $flashThree = padCurl ( [ 'url' => $flashUrl, 'cookies' => $flashJar ] );

  $flashResult = $flashOne ['result'] . ' sign: ' . ( $flashJar ['padFlash'] ?: 'none' )
               . ' | ' . trim ( $flashTwo ['data'] )
               . ' sign: ' . ( ( $flashTwo ['cookies'] ['padFlash'] ?? '' ) === 'deleted' ? 'taken' : 'kept' )
               . ' | ' . trim ( $flashThree ['data'] );

?>
