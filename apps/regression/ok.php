<?php

  // Accepts what a page renders now as its baseline, which is what the crawl will compare against
  // next time.
  //
  // The fetch has to match the crawl's exactly or accepting achieves nothing: getRegressionGo()
  // asks for &padInclude on everything but an index, and this asked for &include - which is not a
  // flag PAD reads, so the whole wrapped page was stored where a bare one was expected. The
  // status went to ok and the next crawl put it straight back to warning.

  $include = ( $item != 'index' ) ? '&padInclude' : '';

  $curl = padCurl ( "$padHost$app/?$item$include" );

  padFilePut ( DATA . "regression/$app/$item.html", $curl ['data'] );
  padFilePut ( DATA . "regression/$app/$item.txt",  'ok'            );

  padRedirect ( 'index' );

?>