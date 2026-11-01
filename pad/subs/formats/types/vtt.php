<?php

  // vtt - WebVTT (W3C, WebVTT: The Web Video Text Tracks Format), read and written: a cue a
  // row - id, start, end, startms, endms, text, plain, voice and settings
  // (subs/formats/_lib/cues.php) - and rows as the cues of a <track>, sorted by their start:
  // ?talk&padFormat=vtt is the src of a video's captions or chapters. A NOTE, a STYLE and a
  // REGION block are passed over. Known on sight: a text that opens with the line WEBVTT.

  return [
    'name'      => 'WebVTT',
    'mime'      => 'text/vtt',
    'extension' => [ 'vtt' ],
    'shape'     => 'rows',
    'lib'       => 'cues',
    'read'      => fn ( $text, $options ) => padVttRead ( $text ),
    'write'     => fn ( $data, $options ) => padVttWrite ( padFormatRows ( $data ) ),
    'sniff'     => [ 'csv', fn ( $text ) => (bool) preg_match ( '/\AWEBVTT(?:[ \t][^\n]*)?\r?\n/', $text ) ],
    'download'  => 'inline'
  ];

?>
