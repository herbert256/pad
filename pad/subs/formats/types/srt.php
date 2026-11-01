<?php

  // srt - SubRip subtitles (SRT, as the Library of Congress describes it, fdd000569), read and
  // written: a subtitle a row, as WebVTT's cues are - id (its number), start, end, startms,
  // endms, text, plain, voice and settings (subs/formats/_lib/cues.php) - and rows as
  // subtitles numbered from 1 in the order of their start, 00:00:01,000 --> 00:00:04,000,
  // with CRLF line ends. A text that is no UTF-8 is read as Windows-1252, the encoding of a
  // legacy SRT file. Known on sight: a first line of a number and a timing under it.

  // SRT has no registered media type; application/x-subrip is the one in use.

  return [
    'name'      => 'SRT',
    'mime'      => 'application/x-subrip',
    'extension' => [ 'srt' ],
    'shape'     => 'rows',
    'lib'       => 'cues',
    'read'      => fn ( $text, $options ) => padSrtRead ( $text ),
    'write'     => fn ( $data, $options ) => padSrtWrite ( padFormatRows ( $data ) ),
    'sniff'     => [ 'csv', fn ( $text ) => (bool) preg_match ( '/\A[ \t]*\d+[ \t]*\r?\n[ \t]*\d{1,9}:\d\d:\d\d[,.]\d{3}[ \t]*-->[ \t]*\d{1,9}:\d\d:\d\d[,.]\d{3}/', $text ) ],
    'download'  => 'inline'
  ];

?>
