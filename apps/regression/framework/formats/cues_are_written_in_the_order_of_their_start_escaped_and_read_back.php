<?php

  // Rows written as WebVTT and as SRT (line ends shown as ~): the cues in the order of their
  // start, a start read from a timestamp of any length, a number of seconds or startms, an end
  // left out the start of the next cue - chapters; WebVTT's & < > of a plain title escaped, a
  // tag it has kept and SRT's <font> and {\an8} taken out, a blank line and an arrow of a text
  // never ending the cue, an identifier one line, only the settings of each format kept; SRT
  // numbered from 1 with a comma before the milliseconds, WebVTT's character references the
  // characters, its own tags out, an arrow ->. Read back, both give the times written and the
  // plain text - and the SRT written again from what it read is the same bytes.

  $cueRows = [
    [ 'start' => '1:30', 'title' => 'Formats & <cues>' ],
    [ 'start' => 0, 'title' => 'Intro', 'id' => "chapter\n1 --> a" ],
    [ 'startms' => 125500, 'endms' => 180000, 'text' => "{\\an8}<c.loud>Cues</c> &amp; <font color=\"red\">rows</font> <00:02:06.000>a < b\n\n--> next", 'settings' => 'align:start X1:5 line:0 color:red' ]
  ];

  $cueVtt = padFormatWrite ( 'vtt', $cueRows );
  $cueSrt = padFormatWrite ( 'srt', $cueRows );

  $cueShow = str_replace ( [ "\r\n", "\n" ], [ '~', '~' ], $cueVtt ) . ' ## ' . str_replace ( "\r\n", '~', $cueSrt );

  $cueTimes = fn ( $rows ) => implode ( ' ', array_map ( fn ( $row ) => "{$row['startms']}-{$row['endms']}=" . str_replace ( "\n", '/', $row ['plain'] ), $rows ) );

  $cueBack = 'read back: ' . $cueTimes ( padFormatRead ( 'vtt', $cueVtt ) ) . ' ## ' . $cueTimes ( padFormatRead ( 'srt', $cueSrt ) );

  $cueSame = ( $cueSrt === padFormatWrite ( 'srt', padFormatRead ( 'srt', $cueSrt ) ) ) ? 'the same bytes again' : 'other bytes';

?>
