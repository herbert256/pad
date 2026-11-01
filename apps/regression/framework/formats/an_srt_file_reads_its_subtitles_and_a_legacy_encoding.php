<?php

  // SRT read as players read it: the Library of Congress's example with its CRLF line ends, a
  // blank line inside a text with no number and timing behind it going on with the text, a
  // subtitle without its number, a dot before the milliseconds, SRT's coordinates as its
  // settings, its <font> and {\an8} out of plain; a file of Windows-1252 bytes - no UTF-8 -
  // read as that encoding. The rows written as WebVTT are its cues, the numbers their
  // identifiers. Each subtitle: id|start|end|text|plain|settings.

  $srtText = "1\r\n00:02:16,612 --> 00:02:19,376\r\nSenator, we're making\r\nour final approach into Coruscant.\r\n\r\n"
           . "2\r\n00:02:19,482 --> 00:02:21,609\r\nVery good, Lieutenant.\r\n\r\nstill the lieutenant\r\n\r\n"
           . "00:02:22.000 --> 00:02:23.500 X1:100 X2:200 Y1:10 Y2:20\r\n{\\an8}<font color=\"#ffff00\">Top</font>\r\n\r\n"
           . "4\r\n00:02:24,000 --> 00:02:25,000\r\nCaf\xe9 \x93quoted\x94";

  $srtOut = [];

  foreach ( padFormatRead ( 'srt', $srtText ) as $srtRow )
    $srtOut [] = "{$srtRow['id']}|{$srtRow['start']}|{$srtRow['end']}|" . str_replace ( "\n", '/', $srtRow ['text'] ) . '|'
               . str_replace ( "\n", '/', $srtRow ['plain'] ) . "|{$srtRow['settings']}";

  $srtShow = implode ( ' ## ', $srtOut );
  $srtVtt  = str_replace ( "\n", '~', padFormatWrite ( 'vtt', padFormatRead ( 'srt', $srtText ) ) );

?>
