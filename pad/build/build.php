<?php

  // Assembles the complete page source, then hands it to the tag engine.
  //
  // Runs the build steps in order: dirs (the directory chain), libs (the _lib content),
  // guards (the _guard.php of each directory), base (the nested _inits.pad/@page@/_exits.pad
  // frame) and page (the page's own PHP plus its .pad), then expose (a request for the
  // page's data, which renders none of them). The page is dropped into the frame's @page@
  // hole, the whole thing becomes $padBase [$pad], and occurrence/occurrence.php starts the
  // first pass over it.

  // The page renders once: its own @start@ and @end@ come out (occurrence/init.php).

  $padSectionsOnce [$pad] = TRUE;

  include PAD . 'build/dirs.php';

  $padBuildLib  = include PAD . 'build/libs.php';

  // The directory guards decide before any _inits.php runs. A refused request is answered
  // 403; a page a {page} tag includes renders as nothing when its guard refuses - an
  // allowed page must not show a guarded one's content, nor be refused for holding it.

  include PAD . 'build/guards.php';

  if ( $padBuildRefused and empty ( $padGuardNested ) )
    padRefuse ( 403, padLocal ()
      ? 'Forbidden: ' . str_replace ( APPS, '', $padBuildRefused ) . " refused the page '$padPage'"
      : 'Forbidden' );

  // A page-cache hit of a page with {nocache} parts renders the stored page with the parts
  // in it - cache/inits.php found it - in place of building the page: no _inits.php and no
  // page PHP run. Only the request's own page; a {page} it includes builds as ever.

  if ( $padBuildRefused )

    $padBase [$pad] = '';

  elseif ( ( $GLOBALS ['padNocacheHit'] ?? NULL ) and ! $GLOBALS ['padNocacheBuilt'] ) {

    $GLOBALS ['padNocacheBuilt'] = TRUE;

    $padBase [$pad] = $padBuildLib . padNocacheTemplate ( $GLOBALS ['padNocacheHit'] [0], $GLOBALS ['padNocacheHit'] [1] );

  } else {

    $padBuildBase = include PAD . 'build/base.php';
    $padBuildPage = include PAD . 'build/page.php';

    include PAD . 'build/expose.php';

    $padBase [$pad] = $padBuildLib . str_replace ( '@page@', $padBuildPage, $padBuildBase );

    // The same text as source-map pieces, kept as this level's map when they join up to it
    // exactly - what lets an error name the template file, line and column (lib/source.php).

    $padSrcMap [$pad] = padSrcMake (
      array_merge ( $padSrcLib, padSrcReplace ( $padSrcBase, '@page@', $padSrcPage ) ),
      $padBase [$pad],
      $padSrcWrap
    );

  }

  // Strict mode reads the assembled source for construct typos: an @word@ that names no
  // file in pad/constructs/ renders as nothing anywhere, silently. What sits between
  // {ignore} tags is the author saying hands off, so it stays out of the scan.

  if ( $padCheckSyntax ) {

    $padBuildScan = preg_replace ( '/\{ignore\}.*?\{\/ignore\}/s', '', $padBase [$pad] );

    if ( preg_match_all ( '/@([a-zA-Z][a-zA-Z0-9]*)@/', $padBuildScan, $padBuildCon ) )
      foreach ( array_unique ( $padBuildCon [1] ) as $padBuildOne )
        if ( ! file_exists ( PAD . "constructs/$padBuildOne.php" ) )
          padErrorAt ( "there is no @" . $padBuildOne . "@ construct",
                       [ 'level'  => $pad,
                         'base'   => strpos ( $padBase [$pad], "@$padBuildOne@" ),
                         'length' => strlen ( $padBuildOne ) + 2 ] );

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
