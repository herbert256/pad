<?php

  // Assembles the complete page source, then hands it to the tag engine.
  //
  // Runs the build steps in order: dirs (the directory chain), libs (the _lib content),
  // base (the nested _inits.pad/@page@/_exits.pad frame) and page (the page's own PHP
  // plus its .pad). The page is dropped into the frame's @page@ hole, the whole thing
  // becomes $padBase [$pad], and occurrence/occurrence.php starts the first pass over it.

  include PAD . 'build/dirs.php';

  $padBuildLib  = include PAD . 'build/libs.php';
  $padBuildBase = include PAD . 'build/base.php';
  $padBuildPage = include PAD . 'build/page.php';

  $padBase [$pad] = $padBuildLib . str_replace ( '@page@', $padBuildPage, $padBuildBase );

  // Strict mode reads the assembled source for construct typos: an @word@ that names no
  // file in pad/constructs/ renders as nothing anywhere, silently. What sits between
  // {ignore} tags is the author saying hands off, so it stays out of the scan.

  if ( $padCheckSyntax ) {

    $padBuildScan = preg_replace ( '/\{ignore\}.*?\{\/ignore\}/s', '', $padBase [$pad] );

    if ( preg_match_all ( '/@([a-zA-Z][a-zA-Z0-9]*)@/', $padBuildScan, $padBuildCon ) )
      foreach ( array_unique ( $padBuildCon [1] ) as $padBuildOne )
        if ( ! file_exists ( PAD . "constructs/$padBuildOne.php" ) )
          return padError ( "there is no @" . $padBuildOne . "@ construct" );

  }

  // Every page leaves through the frame's @page@ hole, so the construct is a fact of the
  // assembly rather than a spot in the template - recorded straight past the source
  // filter, the way configuration values are.

  global $padInfoXref;

  if ( ( $padInfoXref ?? FALSE ) and function_exists ( 'padInfoXrefGo' ) )
    padInfoXrefGo ( 'constructs', 'page', '' );

  if ( $padInfo )
    include PAD . 'events/build.php';

  include PAD . 'occurrence/occurrence.php';

?>