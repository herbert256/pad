<?php

  // padBack follows the Referer only to a page of this application on this host: another
  // page of it, in the query or the path form, is where the redirect goes - rebuilt on
  // $padHost - and every other referrer goes to the fallback: none at all, another host,
  // another application of this host, an application whose name only starts like this
  // one's, a user@host address that is really another host, and a scheme other than http
  // and https.

  $backAsk = function ( $referer ) use ( $padGoExt, $padHost ) {
    $back = padCurl ( [ 'url'     => $padGoExt . 'helpers/requests_back_go',
                        'headers' => [ 'Referer' => $referer ],
                        'options' => [ 'FOLLOWLOCATION' => FALSE, 'REFERER' => '' ] ] );
    return $back ['result'] . ' ' . str_replace ( $padHost, '/', $back ['headers'] ['Location'] ?? 'none' );
  };

  $backResult = implode ( ' | ', [
    $backAsk ( $padGoExt . 'helpers/requests_input_echo&from=here' ),
    $backAsk ( $padHost . 'regression/pages/index.php/helpers/requests_input_echo?from=path' ),
    $backAsk ( '' ),
    $backAsk ( 'http://example.com' . parse_url ( $padGoExt, PHP_URL_PATH ) . '?helpers/x' ),
    $backAsk ( $padHost . 'demo/?index' ),
    $backAsk ( rtrim ( $padGoExt, '/?' ) . '2/?index' ),
    $backAsk ( str_replace ( '://', '://' . parse_url ( $padHost, PHP_URL_HOST ) . '@', 'http://example.com/' ) ),
    $backAsk ( 'ftp' . substr ( $padGoExt, strpos ( $padGoExt, '://' ) ) . 'index' ),
  ] );

?>
