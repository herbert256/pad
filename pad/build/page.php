<?php

  // Runs the PHP side of the request and returns the page's own template text.
  //
  // Execution order is _common/_inits.php, then _inits.php down the $padBuildDirs chain,
  // then the page's own <page>.php, then _exits.php back up the chain and
  // _common/_exits.php - the two _common files only when $padCommon is on, as for its
  // _lib and _inits.pad. Whatever those files echo is kept as content; the page's return
  // value decides the rest - an array becomes the page data ($padBuild), a scalar is
  // appended as content, NULL drops the page's content and template (the _exits.php chain
  // still runs), FALSE selects the @else@ half.
  //
  // The .pad template is appended, build/split.php cuts the text at an @else@, and unless
  // the page produced no data of its own the result is wrapped in {padBuild for="..."} so
  // the level engine iterates $padBuild, one occurrence per row.
  //
  // Where the template sits in that text - $padSrcAt, $padSrcSize, $padSrcFile - is kept
  // for the source map: what this returns becomes $padSrcPage, the template as its own
  // piece among what the PHP files printed (lib/source.php).

  $padBuildTrue  = '';
  $padBuildFalse = '';
  $padSrcAt      = 0;
  $padSrcSize    = 0;
  $padSrcFile    = '';

  if ( $padCommon ) {
    $padCall = COMMON . '/_inits.php';
    $padBuildTrue .= include PAD . 'call/noOne.php';
  }

  foreach ( $padBuildDirs as $padCall ) {
    $padCall .= '/_inits.php';
    $padBuildTrue .= include PAD . 'call/noOne.php';
  }

  $padCall = APP . "$padPage.php";
  $padBuildTrue .= include PAD . 'call/obNoOne.php';
  $padBuildCall = $padCallPHP;

  if ( $padBuildCall !== NULL ) {

    if ( is_array ( $padCallPHP ) ) $padBuild = padData ( $padCallPHP );
    else                            $padBuild = padDefaultData();

    if ( ! is_array ($padCallPHP) and $padCallPHP !== TRUE and $padCallPHP !== FALSE )
      $padBuildTrue .= $padCallPHP;

    $padSrcAt      = strlen ( $padBuildTrue );
    $padSrcFile    = file_exists ( APP . "$padPage.pad" ) ? APP . "$padPage.pad" : APP . "$padPage.html";
    $padBuildTrue .= padPageTemplate ( APP . $padPage );
    $padSrcSize    = strlen ( $padBuildTrue ) - $padSrcAt;

  }

  // The _exits.php chain runs for a page that drops itself too. It returned before them,
  // while the _inits.pad and _exits.pad wrappers still rendered round the empty page - and
  // read whatever the skipped _exits.php files would have set: the manual's 500 on z33.

  foreach ( array_reverse ($padBuildDirs) as $padCall ) {
    $padCall .= '/_exits.php';
    $padBuildTrue .= include PAD . 'call/noOne.php';
  }

  if ( $padCommon ) {
    $padCall = COMMON . '/_exits.php';
    $padBuildTrue .= include PAD . 'call/noOne.php';
  }

  $padSrcPage = [];

  if ( $padBuildCall === NULL )
    return '';

  $padSrcFull = strlen ( $padBuildTrue );

  include PAD . 'build/split.php';

  if ( $padBuildCall === FALSE or ! count ($padBuild) ) {
    $padSrcFrom = $padSrcFull - strlen ( $padBuildFalse );
    $padSrcPage = padSrcPage ( $padBuildFalse, 0, $padSrcFrom, strlen ( $padBuildFalse ), $padSrcAt, $padSrcSize, $padSrcFile );
    return $padBuildFalse;
  }

  if ( padIsDefaultData ( $padBuild) ) {
    $padSrcPage = padSrcPage ( $padBuildTrue, 0, 0, strlen ( $padBuildTrue ), $padSrcAt, $padSrcSize, $padSrcFile );
    return $padBuildTrue;
  }

  $padBuildWrap = "{padBuild for=\"$padPage\"}$padBuildTrue{/padBuild}";
  $padSrcPage   = padSrcPage ( $padBuildWrap, strlen ( "{padBuild for=\"$padPage\"}" ), 0, strlen ( $padBuildTrue ), $padSrcAt, $padSrcSize, $padSrcFile );

  return $padBuildWrap;

?>