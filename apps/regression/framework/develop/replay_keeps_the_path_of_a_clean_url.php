<?php

  // A clean URL is recorded with its path: the page the path named is the one replayed. The
  // query string alone was kept - padInclude here - and the case was replayed as the
  // application's index, which answered something else.

  $replayStore = 'fw' . padRandomString ( 8 );

  padCurl ( $padHost . "regression/framework/index.php/develop/a_page_whose_answer_is_replayed?padInclude&padRecord=$replayStore" );

  $replayCases = padReplayCases ( $replayStore );
  $replayRun   = padReplayRun   ( $replayStore );

  padReplayDelete ( $replayStore );

  $replayOne = reset ( $replayCases ) ?: [];
  $recorded  = count ( $replayCases ) . ' case: ' . ( $replayOne ['path'] ?? '' ) . ' ? ' . ( $replayOne ['query'] ?? '' );
  $replayed  = ( $replayRun [0] ['same'] ?? FALSE ) ? 'same' : 'changed, status ' . ( $replayRun [0] ['status'] ?? '' );

?>
