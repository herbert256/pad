<?php

  // A fetched body is read in the format of the formats subsystem its media type names - the
  // NDJSON of a stream API (application/x-ndjson), a calendar (text/calendar), TOML
  // (application/toml), TSV (text/tab-separated-values) - where application/x-ndjson was
  // taken for one JSON document by the json in its name and failed, and TOML read as CSV;
  // a feed (application/rss+xml) is the XML it is, as before, and JSON is JSON.

  padCurlFake ( [
    'https://api.example.com/ndjson' => [ 'data' => "{\"name\":\"rex\"}\n{\"name\":\"tom\"}", 'headers' => [ 'Content-Type' => 'application/x-ndjson; charset=utf-8' ] ],
    'https://api.example.com/ics'    => [ 'data' => "BEGIN:VCALENDAR\nBEGIN:VEVENT\nSUMMARY:walk\nDTSTART:20261101\nEND:VEVENT\nEND:VCALENDAR", 'headers' => [ 'Content-Type' => 'text/calendar' ] ],
    'https://api.example.com/toml'   => [ 'data' => "[[pets]]\nname = \"rex\"\n[[pets]]\nname = \"tom\"", 'headers' => [ 'Content-Type' => 'application/toml' ] ],
    'https://api.example.com/tsv'    => [ 'data' => "name\nrex\ntom", 'headers' => [ 'Content-Type' => 'text/tab-separated-values' ] ],
    'https://api.example.com/rss'    => [ 'data' => '<rss><channel><item><title>a</title></item></channel></rss>', 'headers' => [ 'Content-Type' => 'application/rss+xml' ] ],
    'https://api.example.com/json'   => [ 'data' => '[{"name":"rex"}]', 'headers' => [ 'Content-Type' => 'application/json' ] ]
  ] );

  $fetchShow = [];

  foreach ( [ 'ndjson', 'ics', 'toml', 'tsv', 'rss', 'json' ] as $fetchKind ) {
    $fetchCurl   = padCurl ( "https://api.example.com/$fetchKind" );
    $fetchShow [] = "$fetchKind: {$fetchCurl ['type']} " . json_encode ( padCurlData ( $fetchCurl, 'x' ) );
  }

  padCurlFakeStop ();

  $fetchShow = implode ( ' | ', $fetchShow );

?>
