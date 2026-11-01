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

    // A built-in tag is filed under the name its file has. On a file system that ignores case a
    // tag written in another case - {Keep}, which the errors suite writes to show it is no tag
    // on a server that tells case apart - reached pad/tags/keep.php, and filed as Keep.txt it
    // took the place of keep.txt: the index of tags listed Keep and lost keep.

    if ( $dir1 == 'tag' and $dir2 == 'pad' and ! isset ( pqNames ( PAD . 'tags' ) ["$dir3.php"] ) )
      return;

    // A kind of a subsystem - a toy, an icon, a map view, a rule, a format - is filed whatever the text
    // of the page says: its name comes from a value as often as from the template, {toy $toy}
    // on the page of each toy, {icon $name} of a gallery, and the page runs it all the same.
    // Looked for in the text, the toys and the games of their applications' pages and the
    // icons of a list were on no page of the reference.

    if ( isset ( padSubsystems [$dir1] ) or isset ( padSubsystemKinds [$dir1] ) ) return padInfoXrefGo ( $dir1, $dir2, $dir3 );

    if ( $dir1 == 'tag'       and $dir2 != 'pad' ) return padInfoXrefGo ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'functions' and $dir2 != 'pad' ) return padInfoXrefGo ( $dir1, $dir2, $dir3 );

    if (   $dir3 and strpos ( $padInfoXrefSource, $dir3 ) === FALSE ) return;
    if ( ! $dir3 and strpos ( $padInfoXrefSource, $dir2 ) === FALSE ) return;

    padInfoXrefGo ( $dir1, $dir2, $dir3 );

  }

  function padInfoXrefGo ( $dir1, $dir2, $dir3 ) {

    global $padApp, $padStartPage, $padPageAsked;

    // A property is filed under its name, which has the shape of a tag name. The August
    // harvest filed properties/.txt, properties/box/.txt (from {box/}), ?.txt, <=.txt and
    // >.txt; the current engine passes none of them - a fresh harvest made none - and
    // this keeps it so.

    if ( $dir1 == 'properties' and ! preg_match ( '/^[A-Za-z][A-Za-z0-9_:#-]*$/D', (string) $dir2 ) )
      return;

    if ( $dir1 == 'properties' and ! file_exists ( PAD . "properties/$dir2.php" ) )
      $dir2 = strtolower($dir2);

    $file = "$dir1/$dir2";

    if ( $dir3 !== '' )
      $file .= "/$dir3";

    // A page of a bracketed route is filed by the name it was asked by - nim, not [game] - as
    // that is a page the reference can show; under the name of its file the reference's page
    // of the item ended on "there is no page named '[game]'".

    $page = ( str_contains ( (string) $padStartPage, '[' ) and ( $padPageAsked ?? '' ) !== '' ) ? $padPageAsked : $padStartPage;

    $target = "reference/$file.txt";
    $xref   = "$padApp;$page";

    $check = DATA . $target;
    if ( file_exists ($check) and in_array ( $xref, file ( $check, FILE_IGNORE_NEW_LINES ) ) )
      return;

    padFilePut ( $target, $xref, 1);

  }

?>
