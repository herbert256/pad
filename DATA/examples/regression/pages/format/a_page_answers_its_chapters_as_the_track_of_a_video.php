<?php

  // The chapters format/chapters exposes, asked for as WebVTT - the src of a <track> - and
  // as SRT: the status, the content type, the file name and the body, the end of a chapter
  // the start of the next; a WebVTT title's & escaped, SRT's as it is.

  $cuesOut = [];

  foreach ( [ 'vtt', 'srt' ] as $cuesKind ) {
    $cuesAnswer = padCurl ( $padGoExt . "format/chapters&padFormat=$cuesKind" );
    $cuesOut [] = "== $cuesKind: " . $cuesAnswer ['result'] . ' ' . ( $cuesAnswer ['headers'] ['Content-Type'] ?? '-' )
                . ' | ' . ( $cuesAnswer ['headers'] ['Content-Disposition'] ?? '-' ) . "\n" . trim ( $cuesAnswer ['data'] );
  }

  echo implode ( "\n", $cuesOut );

?>
