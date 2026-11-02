<?php

  // A fetched body of application/json-seq is read as the JSON text sequence it is - where it
  // was CSV, one column named after its first record - and one of text/vtt as the cues of
  // its captions, by the formats whose media type it is (padFormatMedia).

  padCurlFake ( [
    'https://api.example.com/seq' => [ 'data' => "\x1E{\"name\":\"rex\"}\n\x1E{\"name\":\"tom\"}\n", 'headers' => [ 'Content-Type' => 'application/json-seq' ] ],
    'https://api.example.com/vtt' => [ 'data' => "WEBVTT\n\n00:01.000 --> 00:02.000\nHello", 'headers' => [ 'Content-Type' => 'text/vtt; charset=utf-8' ] ]
  ] );

  $fetchShow = [];

  foreach ( [ 'seq', 'vtt' ] as $fetchKind ) {
    $fetchCurl    = padCurl ( "https://api.example.com/$fetchKind" );
    $fetchRows    = padCurlData ( $fetchCurl, 'x' );
    $fetchShow [] = "$fetchKind: {$fetchCurl ['type']} " . json_encode ( $fetchKind == 'vtt' ? array_column ( $fetchRows, 'text', 'start' ) : $fetchRows );
  }

  padCurlFakeStop ();

  $fetchShow = implode ( ' | ', $fetchShow );

?>
