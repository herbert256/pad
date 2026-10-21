<?php

  // Builds the frame the page is rendered into: the _inits.pad and _exits.pad of every
  // directory in $padBuildDirs, nested outermost first, with one @page@ hole left in the
  // middle for the page itself.
  //
  // An @page@ inside a comment is no marker (padLayoutNoted, lib/layout.php) - a wrapper
  // that mentioned it in a {# #} note was refused as one with two, and one whose only
  // @page@ stood in a commented-out line put the page in the comment, which dropped it.
  //
  // A directory that writes its own @page@ in _inits.pad or _exits.pad decides where the
  // inner levels land; when neither contains one, @page@ is appended to _inits.pad so
  // _exits.pad closes below the page. With $padCommon the result is wrapped once more in
  // the _common app's _inits.pad. Include requests ($padInclude) get a bare '@page@' and
  // no wrappers at all, since their output is a fragment.
  //
  // The same frame is assembled as source-map pieces in $padSrcBase, the marker as text no
  // template holds, and the _inits.pad files that hold something are listed in $padSrcWrap -
  // what an error in the page says it is wrapped by (lib/source.php).

  $padBuildBase = '@page@';
  $padSrcBase   = padSrcPieces ( '@page@' );
  $padSrcWrap   = [];

  if ( $padInclude )
    return $padBuildBase;

  foreach ( $padBuildDirs as $padBuildDir ) {

    $padBuildInit = padLayoutNoted ( padFileGet ( "$padBuildDir/_inits.pad" ) );
    $padBuildExit = padLayoutNoted ( padFileGet ( "$padBuildDir/_exits.pad" ) );

    $padSrcInit = padSrcPieces ( $padBuildInit, "$padBuildDir/_inits.pad" );
    $padSrcExit = padSrcPieces ( $padBuildExit, "$padBuildDir/_exits.pad" );

    if ( trim ( $padBuildInit ) !== '' )
      $padSrcWrap [] = "$padBuildDir/_inits.pad";

    // Two @page@ holes in one wrapper and the whole page renders twice, silently. One
    // marker to a wrapper; strict mode says which wrapper broke the rule.

    if ( $padCheckSyntax and substr_count ( $padBuildInit . $padBuildExit, '@page@' ) > 1 ) {
      $padSrcTwo = substr_count ( $padBuildInit, '@page@' ) > 1 ? 'init' : 'exit';
      padErrorAt ( "one @page@ to a wrapper - " . str_replace ( APPS, '', $padBuildDir ) . " holds more",
                   [ 'file'   => $padSrcTwo == 'init' ? "$padBuildDir/_inits.pad" : "$padBuildDir/_exits.pad",
                     'pos'    => $padSrcTwo == 'init' ? strpos ( $padBuildInit, '@page@', strpos ( $padBuildInit, '@page@' ) + 1 )
                                                      : strrpos ( $padBuildExit, '@page@' ),
                     'length' => 6 ] );
    }

    if ( strpos($padBuildInit, '@page@') === FALSE and strpos($padBuildExit, '@page@') === FALSE  ) {
      $padBuildInit .= '@page@';
      $padSrcInit    = array_merge ( $padSrcInit, padSrcPieces ( '@page@' ) );
    }

    if ( strpos($padBuildInit, '@page@') !== FALSE ) {
      $padBuildBaseNow = str_replace ( '@page@', "@page@$padBuildExit", $padBuildInit );
      $padSrcNow       = padSrcReplace ( $padSrcInit, '@page@', array_merge ( padSrcPieces ( '@page@' ), $padSrcExit ) );
    } else {
      $padBuildBaseNow = str_replace ( '@page@', "$padBuildInit@page@", $padBuildExit );
      $padSrcNow       = padSrcReplace ( $padSrcExit, '@page@', array_merge ( $padSrcInit, padSrcPieces ( '@page@' ) ) );
    }

    $padBuildBase = str_replace ( '@page@', $padBuildBaseNow, $padBuildBase );
    $padSrcBase   = padSrcReplace ( $padSrcBase, '@page@', $padSrcNow );

  }

  if ( ! $padCommon )
    return $padBuildBase;

  // A _common _inits.pad without @page@ - or none at all, in a _common holding only its
  // _include/ or _tags/ - has the page after it, as a directory's has: the page went into
  // a hole that was not there, and every page of the application rendered empty.

  $padBuildCommon = padLayoutNoted ( padFileGet ( COMMON . '_inits.pad' ) );
  $padSrcCommon   = padSrcPieces ( $padBuildCommon, COMMON . '_inits.pad' );

  // Listed by what the file holds, before the @page@ is added - tested after, a _common
  // without an _inits.pad had every error say the page was wrapped by that missing file.

  if ( trim ( $padBuildCommon ) !== '' )
    array_unshift ( $padSrcWrap, COMMON . '_inits.pad' );

  if ( strpos ( $padBuildCommon, '@page@' ) === FALSE ) {
    $padBuildCommon .= '@page@';
    $padSrcCommon    = array_merge ( $padSrcCommon, padSrcPieces ( '@page@' ) );
  }

  $padSrcBase = padSrcReplace ( $padSrcCommon, '@page@', $padSrcBase );

  return str_replace ( '@page@', $padBuildBase, $padBuildCommon );

?>
