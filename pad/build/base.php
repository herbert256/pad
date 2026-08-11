<?php

  // Builds the frame the page is rendered into: the _inits.pad and _exits.pad of every
  // directory in $padBuildDirs, nested outermost first, with one @page@ hole left in the
  // middle for the page itself.
  //
  // A directory that writes its own @page@ in _inits.pad or _exits.pad decides where the
  // inner levels land; when neither contains one, @page@ is appended to _inits.pad so
  // _exits.pad closes below the page. With $padCommon the result is wrapped once more in
  // the _common app's _inits.pad. Include requests ($padInclude) get a bare '@page@' and
  // no wrappers at all, since their output is a fragment.

  $padBuildBase = '@page@';

  if ( $padInclude )
    return $padBuildBase;

  foreach ( $padBuildDirs as $padBuildDir ) {

    $padBuildInit = padFileGet ( "$padBuildDir/_inits.pad" );
    $padBuildExit = padFileGet ( "$padBuildDir/_exits.pad" );

    // Two @page@ holes in one wrapper and the whole page renders twice, silently. One
    // marker to a wrapper; strict mode says which wrapper broke the rule.

    if ( $padCheckSyntax and substr_count ( $padBuildInit . $padBuildExit, '@page@' ) > 1 )
      padError ( "one @page@ to a wrapper - " . str_replace ( APPS, '', $padBuildDir ) . " holds more" );

    if ( strpos($padBuildInit, '@page@') === FALSE and strpos($padBuildExit, '@page@') === FALSE  )
      $padBuildInit .= '@page@';

    if ( strpos($padBuildInit, '@page@') !== FALSE )
      $padBuildBaseNow = str_replace ( '@page@', "@page@$padBuildExit", $padBuildInit );
    else
      $padBuildBaseNow = str_replace ( '@page@', "$padBuildInit@page@", $padBuildExit );

    $padBuildBase = str_replace ( '@page@', $padBuildBaseNow, $padBuildBase );

  }

  if ( $padCommon )
    return str_replace ( '@page@', $padBuildBase, padFileGet ( COMMON . '_inits.pad' ) );
  else
    return $padBuildBase;

?>