<?php

  // The harvest empties DATA/examples - files git keeps - and fills it again with every page
  // of every application, fetched from $padHost, the name the request's Host header gave,
  // and ?show serves what it stored as it is. Anyone could start it: a visitor asking
  // ?build&go=1 with Host: their own server had their pages stored here, and served from
  // here to everyone who opened an example. It is develop's to start - this machine only
  // (examplesLocal, _lib/build.php).

  if ( ! examplesLocal () )
    padAbort ( 403, 'The harvest of the examples answers this machine only.' );

  if ( isset ( $go ) ) {

    padDeleteDataDir ( DATA . 'examples'  );

    set_time_limit ( 0 );

    examplesBuild ();

    padRedirect ( 'index' );

  }

  $title = 'Build';


?>
