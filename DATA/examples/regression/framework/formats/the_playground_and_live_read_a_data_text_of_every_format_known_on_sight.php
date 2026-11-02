<?php

  // The data text of the playground and of a page the live application saves is read as
  // {pad:data} reads it: a pipe table, TSV, NDJSON, an iCalendar and a vCard - the formats known
  // on sight that have no reader in pad/data/ - give their rows as CSV does, where the
  // reader of the type was included as a file of pad/data/ and the request ended on its
  // missing include. Each post carries the token of the application's own form.

  $fmtTexts = [
    "| name | age |\n|------|-----|\n| rex  | 3   |\n| tom  | 5   |"                                  => '{array:rows}{$name}={$age} {/array:rows}',
    "name\tage\nrex\t3\ntom\t5"                                                                      => '{array:rows}{$name}={$age} {/array:rows}',
    "{\"name\":\"rex\",\"age\":3}\n{\"name\":\"tom\",\"age\":5}"                                     => '{array:rows}{$name}={$age} {/array:rows}',
    "BEGIN:VCALENDAR\nVERSION:2.0\nBEGIN:VEVENT\nSUMMARY:walk\nDTSTART:20261101\nEND:VEVENT\nEND:VCALENDAR" => '{array:rows}{$summary} {$start}{/array:rows}',
    "BEGIN:VCARD\nVERSION:4.0\nFN:Ann Example\nEMAIL:ann@example.com\nEND:VCARD"                     => '{array:rows}{$name} {$email}{/array:rows}',
    "name,age\nrex,3"                                                                                => '{array:rows}{$name}={$age} {/array:rows}'
  ];

  $fmtAsk = function ( $app ) use ( $padHost ) {
    $form  = padCurl ( $padHost . "$app/" );
    $token = preg_match ( '/name="padCsrfToken" value="([0-9a-f]+)"/', $form ['data'], $m ) ? $m [1] : '';
    return [ $token, [ 'PHPSESSID' => $form ['cookies'] ['PHPSESSID'] ?? '' ] ];
  };

  [ $fmtToken, $fmtJar ] = $fmtAsk ( 'live' );

  $fmtPlay = [];

  foreach ( $fmtTexts as $fmtData => $fmtSource )
    $fmtPlay [] = trim ( padCurl ( [ 'url' => $padHost . 'live/?render', 'cookies' => $fmtJar,
                                     'post' => [ 'source' => $fmtSource, 'data' => $fmtData, 'padCsrfToken' => $fmtToken ] ] ) ['data'] );

  [ $fmtToken, $fmtJar ] = $fmtAsk ( 'live' );

  $fmtLive = [];

  foreach ( $fmtTexts as $fmtData => $fmtSource ) {
    $fmtSave = padCurl ( [ 'url' => $padHost . 'live/?save', 'cookies' => $fmtJar, 'options' => [ 'FOLLOWLOCATION' => FALSE ],
                           'post' => [ 'source' => $fmtSource, 'mode' => 'data', 'data' => $fmtData, 'padCsrfToken' => $fmtToken ] ] );
    $fmtLive [] = trim ( padCurl ( [ 'url' => (string) ( $fmtSave ['headers'] ['Location'] ?? '' ), 'cookies' => $fmtJar ] ) ['data'] );
  }

  $fmtAnswer = 'preview: ' . implode ( ' | ', $fmtPlay ) . ' / live: ' . implode ( ' | ', $fmtLive );

?>
