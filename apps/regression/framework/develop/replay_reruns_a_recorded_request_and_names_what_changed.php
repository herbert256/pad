<?php

  $replayStore = 'fw' . padRandomString ( 8 );

  padCurl ( $padHost . "regression/framework/?develop/a_page_whose_answer_is_replayed&padInclude&padRecord=$replayStore" );

  $replayCases = padReplayCases ( $replayStore );
  $replayFirst = padReplayRun   ( $replayStore );

  foreach ( $replayCases as $replayCase ) {
    $replayCase ['body'] = str_replace ( 'apples', 'pears', $replayCase ['body'] );
    padReplaySave ( $replayStore, $replayCase );
  }

  $replaySecond = padReplayRun ( $replayStore );

  padReplayDelete ( $replayStore );

  $recorded = count ( $replayCases ) . ' case: ' . ( reset ( $replayCases ) ['query'] ?? '' );
  $first    = $replayFirst  [0] ['same'] ? 'same' : 'changed';
  $second   = $replaySecond [0] ['same'] ? 'same'
            : 'changed on line ' . $replaySecond [0] ['line'] . ': ' . $replaySecond [0] ['want'] . ' became ' . $replaySecond [0] ['got'];
  $left     = count ( padReplayCases ( $replayStore ) );

?>
