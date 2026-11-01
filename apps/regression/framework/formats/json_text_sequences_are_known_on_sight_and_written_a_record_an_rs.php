<?php

  // A JSON text sequence (RFC 7464) is known on sight by the RS (0x1E) it opens with, a record
  // over many lines read as one - as jq --seq writes them. Written: an RS, the row as JSON
  // writes it and a line feed a record (the RS shown as #), an RS inside a string as \u001e,
  // a plain value a row of its own under value; read again, the same rows. And the input of
  // RFC 7464's examples as jq --seq reads it: two RS side by side no empty record.

  $seqRows = [ [ 'path' => '/a/b', 'name' => 'Zoë', 'note' => "an \x1E inside" ], [ 'list' => [ 1, 2 ] ], 42 ];
  $seqText = padFormatWrite ( 'jsonseq', $seqRows );

  $seqShow = str_replace ( [ "\x1E", "\n" ], [ '#', ' ' ], $seqText );
  $seqBack = ( padFormatRead ( 'jsonseq', $seqText ) === [ $seqRows [0], $seqRows [1], [ 'value' => 42 ] ] ) ? 'read back the same' : 'read back otherwise';
  $seqJq   = json_encode ( padFormatRead ( 'jsonseq', "\x1E\x1E[1,2]\n\x1E\"foo\"\n\x1E{\"a\":true}\n" ) );

?>
