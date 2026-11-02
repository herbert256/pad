<?php

  // The answers of format/events in every format of pad/subs/formats/ that writes - the
  // status, the content type, the file name a browser saves it under and the body (CRLF shown
  // as ~, a spreadsheet as its sheet's XML and the rows read back) - and the Accept headers
  // that ask for them: text/calendar is the calendar, application/rss+xml the feed, a
  // browser's header the page; padFormat=md is markdown by its extension, xml - read only -
  // is 406, and vcf, whose cards the events have no name for, an empty answer and no error,
  // as are vtt and srt, whose cues have no time a date is.

  function formatsAsk ( $query, $accept = '' ) {

    global $padGoExt;

    $input = [ 'url' => $padGoExt . $query ];

    if ( $accept )
      $input ['headers'] = [ 'Accept' => $accept ];

    return padCurl ( $input );

  }

  $formatsOut = [];

  foreach ( [ 'toml', 'yaml', 'ini', 'ndjson', 'jsonseq', 'md', 'tsv', 'ics', 'vcf', 'vtt', 'srt', 'rss', 'atom', 'jsonfeed', 'geojson', 'gpx', 'kml', 'xlsx' ] as $formatsKind ) {

    $formatsAnswer = formatsAsk ( "format/events&padFormat=$formatsKind" );
    $formatsBody   = $formatsAnswer ['data'];

    // A zip's last bytes may be blanks, which padCurl trims off a body: the spreadsheet is
    // fetched as the bytes it is.

    if ( $formatsKind == 'xlsx' ) {
      $formatsCurl = curl_init ( $padGoExt . 'format/events&padFormat=xlsx' );
      curl_setopt ( $formatsCurl, CURLOPT_RETURNTRANSFER, TRUE );
      $formatsBytes = curl_exec ( $formatsCurl );
      $formatsFile  = tempnam ( sys_get_temp_dir (), 'padtest' );
      file_put_contents ( $formatsFile, $formatsBytes );
      $formatsZip   = new ZipArchive ();
      $formatsZip->open ( $formatsFile, ZipArchive::RDONLY );
      $formatsBody  = $formatsZip->getFromName ( 'xl/worksheets/sheet1.xml' ) . "\n" . json_encode ( padFormatRead ( 'xlsx', $formatsBytes ) );
      $formatsZip->close ();
      unlink ( $formatsFile );
    }

    $formatsOut [] = "== $formatsKind: " . $formatsAnswer ['result'] . ' ' . ( $formatsAnswer ['headers'] ['Content-Type'] ?? '-' )
                   . ' | ' . ( $formatsAnswer ['headers'] ['Content-Disposition'] ?? '-' ) . "\n" . str_replace ( "\r\n", "~\n", trim ( $formatsBody ) );

  }

  $formatsType = fn ( $answer ) => $answer ['result'] . ' ' . explode ( ';', $answer ['headers'] ['Content-Type'] ?? '' ) [0];

  $formatsOut [] = '== accept: ' . implode ( ' / ', [
    $formatsType ( formatsAsk ( 'format/events&padInclude', 'text/calendar' ) ),
    $formatsType ( formatsAsk ( 'format/events&padInclude', 'application/rss+xml' ) ),
    $formatsType ( formatsAsk ( 'format/events&padInclude', 'application/json;q=0.5, application/toml' ) ),
    $formatsType ( formatsAsk ( 'format/events&padInclude', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' ) ),
    $formatsType ( formatsAsk ( 'format/events&padInclude', 'text/plain' ) ),
    $formatsType ( formatsAsk ( 'format/events&padFormat=xml' ) ),
    $formatsType ( formatsAsk ( 'format/host&padFormat=ics' ) ) ] );

  echo implode ( "\n", $formatsOut );

?>
