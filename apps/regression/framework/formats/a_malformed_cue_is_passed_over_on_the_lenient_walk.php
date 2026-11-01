<?php

  // With the strict check off, a cue a browser drops is passed over and the others are read: a
  // timing with minutes past 59, a block that is no cue, a cue that ends before it starts - in
  // WebVTT and in SRT, where the reading goes on at the next subtitle behind a blank line. A
  // text without its WEBVTT line is no WebVTT and no rows; a row without a start is left out
  // of what is written, without a word.

  $padCheckSyntax = FALSE;

  $lenientVtt = padFormatRead ( 'vtt', "WEBVTT\n\n00:60.000 --> 00:61.000\nminutes past 59\n\njust text\n\n"
                                     . "00:05.000 --> 00:04.000\nends before it starts\n\nkept\n00:01.000 --> 00:02.000\nkept\n" );

  $lenientSrt = padFormatRead ( 'srt', "1\n00:00:01,000 -> 00:00:02,000\nno arrow\n\n2\n00:00:03,000 --> 00:00:04,000\nkept\n\n"
                                     . "3\n00:00:09,000 --> 00:00:08,000\nends before it starts\n\n4\n00:00:10,000 --> 00:00:11,000\nkept too\n" );

  $lenientNone = count ( padFormatRead ( 'vtt', "00:01.000 --> 00:02.000\nno signature" ) );
  $lenientOut  = substr_count ( padFormatWrite ( 'vtt', [ [ 'text' => 'no start' ], [ 'start' => 1, 'end' => 2, 'text' => 'timed' ] ] ), '-->' );

  $lenientShow = implode ( ' ', array_map ( fn ( $row ) => "{$row['id']}={$row['text']}", $lenientVtt ) ) . ' / '
               . implode ( ' ', array_map ( fn ( $row ) => "{$row['id']}={$row['text']}", $lenientSrt ) )
               . " / no signature: $lenientNone rows / written: $lenientOut cue";

?>
