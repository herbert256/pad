<?php

  // The build empties DATA/reference - files git keeps - and crawls every page of every
  // application again, from $padHost, the name the request's Host header gave. Anyone
  // could start it, and a visitor asking with Host: another server left the reference
  // empty. It is develop's to start - this machine only (referenceLocal, _lib/build.php).

  if ( ! referenceLocal () )
    padAbort ( 403, 'The reference build answers this machine only.' );

  if ( isset ( $go ) ) {

    padDeleteDataDir ( DATA . 'reference'  );

    set_time_limit ( 0 );

    referenceBuild ();

    padRedirect ( 'index' );

  }

  $title = 'Build';


?>
