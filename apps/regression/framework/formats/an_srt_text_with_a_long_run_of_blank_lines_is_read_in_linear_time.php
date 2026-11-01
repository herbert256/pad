<?php

  // A subtitle whose text holds 20000 blank lines and goes on behind them: every blank line
  // of the run looked ahead over the whole run for the subtitle that would end the text, and
  // the next one did so again - 40 KB took seconds. The run is the text's, as before: one
  // subtitle, its text from Hi to the end.

  $srtText = "1\n00:00:01,000 --> 00:00:02,000\nHi\n" . str_repeat ( " \n", 20000 ) . "the end\n\n2\n00:00:03,000 --> 00:00:04,000\nBye";

  $start   = hrtime ( TRUE );
  $srtRows = padFormatRead ( 'srt', $srtText );
  $srtTime = ( hrtime ( TRUE ) - $start < 1e9 ) ? 'in time' : 'too slow';

  $srtShow = count ( $srtRows ) . ' subtitles, the first ' . substr_count ( $srtRows [0] ['text'] ?? '', "\n" ) . ' lines ending in '
           . substr ( $srtRows [0] ['text'] ?? '', -7 ) . ', the second ' . ( $srtRows [1] ['text'] ?? '' );

?>
