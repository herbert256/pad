<?php

  // The recorder of the 'xref' info mode: builds, page by page, the answer to "which app and
  // page uses this tag, type, function, option, property, construct or sequence?".
  //
  // The hooks in pad/events/ - tag, levelStart, parms, option(s), handling, start/end/else/
  // content, functionParms, fieldClassic, sequence - call padInfoXref with a category and one
  // or two names. It drops everything raised while an include or another page is being rendered
  // (padInsideOther, $padPage != $padStartPage) and everything whose name does not occur in the
  // page source captured in $padInfoXrefSource, so only what the template itself asks for is
  // counted; sequences and non-pad tags and functions bypass that source test.
  //
  // padInfoXrefGo appends "<app>;<page>" to DATA/reference/<category>/<name>.txt, once per page.

  function padInfoXref ( $dir1, $dir2, $dir3='' ) {

    global $padInfoXref, $padInfoXrefSource, $padPage, $padStartPage;

    if ( ! $padInfoXref )
      return;

    if ( $dir1 == 'sequence' )
      return padInfoXrefGo ( $dir1, $dir2, $dir3 );

    // A configuration value is a fact about the request, not about a spot in the template:
    // it is never in the page source, and a page that restarts - the file output type does -
    // still ran under it. So none of the filters below apply.

    if ( str_starts_with ( $dir1, 'config' ) ) return padInfoXrefGo ( $dir1, $dir2, $dir3 );

    if ( padInsideOther ()         ) return;
    if ( $padPage != $padStartPage ) return;

    if ( $dir1 == 'tag'       and $dir2 != 'pad' ) return padInfoXrefGo ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'functions' and $dir2 != 'pad' ) return padInfoXrefGo ( $dir1, $dir2, $dir3 );

    if (   $dir3 and strpos ( $padInfoXrefSource, $dir3 ) === FALSE ) return;
    if ( ! $dir3 and strpos ( $padInfoXrefSource, $dir2 ) === FALSE ) return;

    padInfoXrefGo ( $dir1, $dir2, $dir3 );

  }

  function padInfoXrefGo ( $dir1, $dir2, $dir3 ) {

    global $padApp, $padStartPage;

    if ( $dir1 == 'properties' and ! file_exists ( PAD . "properties/$dir2.php" ) )
      $dir2 = strtolower($dir2);

    $file = "$dir1/$dir2";

    if ( $dir3 !== '' )
      $file .= "/$dir3";

    $target = "reference/$file.txt";
    $xref   = "$padApp;$padStartPage";

    $check = DATA . $target;
    if ( file_exists ($check) and in_array ( $xref, file ( $check, FILE_IGNORE_NEW_LINES ) ) )
      return;

    padFilePut ( $target, $xref, 1);

  }

?>