<?php

  // Regenerates the OEIS lookup table from the 'stripped' bulk download published by
  // oeis.org. A maintenance script run by hand, not a step of any sequence build.
  //
  // Each line of the download reads "Annnnnn ,t1,t2,...,": the A-number is taken from
  // columns 1 to 7 and the term list from column 9 up to the trailing comma and newline.
  // Every A-number from 0 to 375610 gets a row, empty where the download has none, and the
  // whole thing is written out as a const OEIS array - to oeis/test.php, deliberately not
  // over the oeis.php in use, which is renamed into place once the new table checks out.
  //
  // The download is expected beside the repository, at dirname($padHome)/host/Downloads.
  //
  // Sharing its name with the build strategy is a trap: pqBuild() would report 'build' for
  // this type and build/types/build.php would run this file expecting a term list back. It
  // does not happen only because make.php outranks build.php in pqBuild().

  global $padHome;

  $w = file ( dirname ( $padHome ) . '/host/Downloads/stripped', TRUE);

  foreach ( $w as $l ) {

    $i = (int) substr ( $l, 1,  7 );

    $a [$i] = substr ( $l, 9, -2 );

  }

  $t = "<?php\n\nconst OEIS = [";

  for ($i=0; $i <=375610 ; $i++) {

    if ( isset ( $a [$i] ) )
      $s = $a [$i];
    else
      $s = '';

    $t .= "\n[$s],";

  }

  $t = substr ( $t, 0, -1) . "\n];\n\n?>";

  padFilePut ( PT . "oeis/test.php", $t );

?>