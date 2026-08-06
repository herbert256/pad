<?php

  // Regenerates the OEIS lookup table from the 'stripped' bulk download published by
  // oeis.org. A maintenance script run by hand, not a step of any sequence build.
  //
  // Each line of the download reads "Annnnnn ,t1,t2,...,": the A-number is taken from
  // columns 1 to 7 and the term list from column 9 up to the trailing comma and newline.
  // The result is an sqlite table of one row per A-number, the term list held as the text it
  // arrived as - written to oeis/test.sqlite, deliberately not over the oeis.sqlite in use,
  // which is renamed into place once the new table checks out.
  //
  // Download and table are read and written a line at a time, so regenerating costs a couple
  // of megabytes rather than the gigabyte the whole thing takes as one array. An A-number
  // the download has no line for gets no row, which is what oeis/read.php answers with an
  // empty sequence.
  //
  // The download is expected beside the repository, at dirname($padHome)/host/Downloads.
  //
  // Sharing its name with the build strategy is a trap: pqBuild() would report 'build' for
  // this type and build/types/build.php would run this file expecting a term list back. It
  // does not happen only because make.php outranks build.php in pqBuild().

  global $padHome;

  $pqOeisFile = dirname ( $padHome ) . '/host/Downloads/stripped';

  if ( ! file_exists ( $pqOeisFile ) )
    return padError ( "OEIS download not found: $pqOeisFile" );

  $pqOeisOut = PT . 'oeis/test.sqlite';

  if ( file_exists ( $pqOeisOut ) )
    unlink ( $pqOeisOut );

  $pqOeisDb = new SQLite3 ( $pqOeisOut );

  $pqOeisDb->exec ( 'PRAGMA journal_mode = OFF' );
  $pqOeisDb->exec ( 'PRAGMA synchronous = OFF' );
  $pqOeisDb->exec ( 'CREATE TABLE oeis ( a INTEGER PRIMARY KEY, terms TEXT NOT NULL )' );

  $pqOeisPut = $pqOeisDb->prepare ( 'INSERT OR REPLACE INTO oeis ( a, terms ) VALUES ( ?, ? )' );

  $pqOeisDb->exec ( 'BEGIN' );

  $pqOeisIn = fopen ( $pqOeisFile, 'r' );

  while ( ( $pqOeisLine = fgets ( $pqOeisIn ) ) !== FALSE ) {

    if ( $pqOeisLine [0] != 'A' )
      continue;

    $pqOeisTerms = trim ( substr ( $pqOeisLine, 9 ), " ,\r\n" );

    if ( $pqOeisTerms === '' )
      continue;

    $pqOeisPut->reset ();
    $pqOeisPut->bindValue ( 1, (int) substr ( $pqOeisLine, 1, 7 ), SQLITE3_INTEGER );
    $pqOeisPut->bindValue ( 2, $pqOeisTerms, SQLITE3_TEXT );
    $pqOeisPut->execute ();

  }

  fclose ( $pqOeisIn );

  $pqOeisDb->exec ( 'COMMIT' );
  $pqOeisDb->close ();

?>