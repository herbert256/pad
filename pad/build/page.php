<?php

  // Runs the PHP side of the request and returns the page's own template text.
  //
  // Execution order is _common/_inits.php, then _inits.php down the $padBuildDirs chain,
  // then the page's own <page>.php, then _exits.php back up the chain and
  // _common/_exits.php - the two _common files only when $padCommon is on, as for its
  // _lib and _inits.pad. Whatever those files echo is kept as content; the page's return
  // value decides the rest - an array becomes the page data ($padBuild), a scalar is
  // appended as content, NULL drops the page's content and template (the _exits.php chain
  // still runs), FALSE selects the @else@ half. Ahead of all of it a post of a {form} is
  // checked against the rules= its fields carry in the page's template (lib/form.php), so
  // padPosted tells the PHP whether the post passed.
  //
  // The .pad template is appended - or the text an entry point handed over in
  // $padPageSource - its {meta} tags applied by lib/meta.php and its layout blocks resolved
  // against the frame by lib/layout.php - build/split.php cuts the text at an @else@, and
  // unless the page produced no data of its own the result is wrapped in
  // {padBuild for="..."} so the level engine iterates $padBuild, one occurrence per row.
  //
  // Where the template sits in that text - $padSrcAt, $padSrcSize, $padSrcFile - is kept
  // for the source map: what this returns becomes $padSrcPage, the template as its own
  // piece among what the PHP files printed (lib/source.php).

  $padBuildTrue  = '';
  $padBuildFalse = '';
  $padSrcAt      = 0;
  $padSrcSize    = 0;
  $padSrcFile    = '';

  // The designer preview (lib/sample.php): with a sample for this page, its entries are the
  // variables and no PHP runs - not the page's, nor an _inits.php or _exits.php around it,
  // whose login check or database would be the reason for the preview. A page a {page} tag
  // builds in a nested pass ($padStrCnt >= 0) uses its own sample when it has one and runs
  // as usual when it has not; the requested page must have one. A capture runs everything
  // as usual and keeps what the PHP of the requested page made.

  // The form rules (lib/form.php) are read from the request's page as it is written, with
  // its frame - before any PHP, which a post of a form is validated ahead of.

  if ( $padStrCnt < 0 ) {
    $padFormText  = $padBuildBase . ( $GLOBALS ['padPageSource'] ?? padPageTemplate ( APP . $padPage ) );
    $padFormRules = NULL;
  }

  $padBuildSample  = ( $padSampleMode == 'use' ) ? padSampleLoad ( $padPage ) : NULL;
  $padBuildCapture = ( $padSampleMode == 'capture' and $padStrCnt < 0 );

  if ( $padSampleMode == 'use' and $padBuildSample === NULL and $padStrCnt < 0 and $padCheckSyntax )
    padError ( "there is no sample for this page - " . str_replace ( APPS, '', padSampleName ( $padPage ) [0] ) );

  if ( $padBuildCapture )
    $padBuildBefore = padSampleBefore ();

  if ( $padBuildSample !== NULL ) {

    $padSampleData = $padBuildSample;
    padSampleUse ( $padSampleData );

    // What the page's PHP returned - its rows, FALSE for the @else@ half - stands in the
    // sample as @return, which no variable can be named: a page returning its data
    // previewed one empty occurrence.

    $padCallPHP   = array_key_exists ( '@return', $padBuildSample ) ? $padBuildSample ['@return'] : TRUE;
    $padBuildCall = $padCallPHP;

  } else {

    if ( $padStrCnt < 0 )
      padFormPost ();

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

  }

  if ( $padBuildCall !== NULL ) {

    if ( is_array ( $padCallPHP ) ) $padBuild = padData ( $padCallPHP );
    else                            $padBuild = padDefaultData();

    if ( ! is_array ($padCallPHP) and $padCallPHP !== TRUE and $padCallPHP !== FALSE )
      $padBuildTrue .= $padCallPHP;

    // A template the entry point handed over as text stands in for the page's file - once:
    // a restart, or a page built inside this one, reads its own file again. It is no file,
    // so the source map leaves it out.

    if ( isset ( $GLOBALS ['padPageSource'] ) ) {
      $padSrcFile  = '';
      $padBuildOwn = $GLOBALS ['padPageSource'];
      unset ( $GLOBALS ['padPageSource'] );
    } else {
      $padSrcFile  = file_exists ( APP . "$padPage.pad" ) ? APP . "$padPage.pad" : APP . "$padPage.html";
      $padBuildOwn = padPageTemplate ( APP . $padPage );
    }

    $padBuildTemplate = padLayoutTilde ( $padBuildOwn );

  } else

    $padBuildTemplate = $padBuildOwn = '';

  // The {meta} tags are applied and the layout blocks resolved now, in the text: a page that
  // extends a layout trades the frame build/base.php made for that layout, and the page's
  // blocks override the frame's - lib/meta.php, lib/layout.php.
  //
  // Those of the half of the page that shows: what the PHP returned decides between the
  // text before the page's @else@ and the text after it (build/split.php), and the half
  // that is not shown sets nothing - a {block 'title'}Not found{/block} after the @else@
  // titled the page over the record that was found, a {meta} there was applied as well,
  // and a block in each half was refused as standing twice.
  //
  // What stands in front of the page's content - the {meta}, {extends} and {block}s of its
  // first lines (padLayoutLead) - is the page's own, written once for both: it goes with the
  // @else@ half as well, where a {block} or an {extends} of the same name that half writes
  // itself takes its place. Taken for the found half only, a page with no rows lost the
  // title of its {meta} on the first line and the layout of its {extends}.

  $padBuildElseAt = ( $padBuildCall !== NULL ) ? padLayoutElse ( $padBuildTemplate ) : FALSE;

  if ( $padBuildElseAt === FALSE ) {

    $padBuildTemplate = padMetaBuild ( $padBuildTemplate );

    padLayout ( $padBuildTemplate, $padBuildBase );

  } else {

    $padBuildHead = substr ( $padBuildTemplate, 0, $padBuildElseAt );
    $padBuildTail = substr ( $padBuildTemplate, $padBuildElseAt + 6 );

    if ( $padBuildCall === FALSE or ! count ( $padBuild ) ) {
      $padBuildLead = padLayoutLead ( $padBuildHead );
      $padBuildTail = padLayoutLeadFor ( substr ( $padBuildHead, 0, $padBuildLead ), $padBuildTail ) . $padBuildTail;
      $padBuildHead = substr ( $padBuildHead, $padBuildLead );
      $padBuildTail = padMetaBuild ( $padBuildTail );
      padLayout ( $padBuildTail, $padBuildBase );
    } else {
      $padBuildHead = padMetaBuild ( $padBuildHead );
      padLayout ( $padBuildHead, $padBuildBase );
    }

    $padBuildTemplate = "$padBuildHead@else@$padBuildTail";

  }

  // The source map (lib/source.php) places the page's template where it lands in the joined
  // text, by its offsets in the file. A page whose {meta} was taken out, or whose {block}s
  // went to a layout, is no longer the file's text from start to end, and an offset into it
  // would name a wrong line, so that page is left out of the map - an error there names no
  // line rather than a wrong one.

  if ( $padBuildTemplate !== $padBuildOwn )
    $padSrcFile = '';

  if ( $padBuildCall !== NULL ) {
    $padSrcAt      = strlen ( $padBuildTrue );
    $padBuildTrue .= $padBuildTemplate;
    $padSrcSize    = strlen ( $padBuildTrue ) - $padSrcAt;
  }

  // The _exits.php chain runs for a page that drops itself too. It returned before them,
  // while the _inits.pad and _exits.pad wrappers still rendered round the empty page - and
  // read whatever the skipped _exits.php files would have set: the manual's 500 on z33.

  if ( $padBuildSample === NULL ) {

    foreach ( array_reverse ($padBuildDirs) as $padCall ) {
      $padCall .= '/_exits.php';
      $padBuildTrue .= include PAD . 'call/noOne.php';
    }

    if ( $padCommon ) {
      $padCall = COMMON . '/_exits.php';
      $padBuildTrue .= include PAD . 'call/noOne.php';
    }

  }

  if ( $padBuildCapture )
    padSampleAfter ( $padBuildBefore, $padBuildCall );

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
