<?php

  $exampleApp        = padTagParm ( 'app' );
  $exampleDir        = ( $exampleApp ) ? APPS . "$exampleApp/" : APP;

  $examplePage       = $padParm ;

  // A page of a bracketed route - reversi of the games application - shows the files of its
  // route, [game].php and [game].pad: by its own name there were none, and its PHP and HTML
  // panels stood empty beside the result.

  $exampleSource     = $examplePage;

  if ( ! file_exists ( "$exampleDir$examplePage.pad" ) and ! file_exists ( "$exampleDir$examplePage.php" )
       and ( $exampleRoute = padPageRoute ( $examplePage, $exampleDir ) ) and $exampleRoute ['vars'] )
    $exampleSource   = $exampleRoute ['page'];

  // The title is the last part of the name. strrpos answers FALSE when the name has no / at
  // all, and FALSE + 1 is 1, so an example named without a directory - {example 'offices'} -
  // used to lose its first letter and come out as "ffices". A title= given on the tag wins:
  // an example of a file rather than a page has no name to take it from.

  $exampleSlash      = strrpos ( $examplePage, '/' );
  $exampleTitle      = ( $exampleSlash === FALSE ) ? $examplePage : substr ( $examplePage, $exampleSlash + 1 );
  $exampleTitle      = padTagParm ( 'title', $exampleTitle );
  $exampleFile       = $exampleDir . $exampleSource;
  $exampleLayout     = padTagParm ( 'layout' , layout ("$exampleFile.pad") );
  $exampleOnlyResult = onlyResult ( "$exampleFile.pad" );

  $examplePhpGiven = padTagParm ( 'php' );
  $examplePadGiven = padTagParm ( 'pad' );

  $examplePhp = $examplePad = $exampleDir;

  $examplePhp      .= ( $examplePhpGiven ) ? $examplePhpGiven : "$exampleSource.php";
  $examplePad      .= ( $examplePadGiven ) ? $examplePadGiven : "$exampleSource.pad";

  $exampleSrcPhp   = ( file_exists($examplePhp) ) ? padColorsFile ($examplePhp) : '';
  $exampleSrcPad   = ( file_exists($examplePad) ) ? padColorsFile ($examplePad) : '';

  $exampleFilePhp  = ( file_exists($examplePhp) ) ? str_replace($exampleDir, '', $examplePhp) : '';
  $exampleFilePad  = ( file_exists($examplePad) ) ? str_replace($exampleDir, '', $examplePad) : '';

  if ( padTagParm ( 'onlyPHP' ) ) {
    $exampleOnlyResult            = '';
    $padPrm [$pad] ['skipPad']    = TRUE;
    $padPrm [$pad] ['skipResult'] = TRUE;
  }

  if ( padTagParm ( 'skipWhenEmpty' ) ) {
    if ( ! padFileGet ( $examplePhp ) ) $padPrm [$pad] ['skipPhp'] = TRUE;
    if ( ! padFileGet ( $examplePad ) ) $padPrm [$pad] ['skipPad'] = TRUE;
  }

  // The icon that takes the example to the playground (_lib/playground.php): its template and
  // its PHP, at the top right of its table - or, in a table an example before it opened,
  // at the right of its own cell. Only where the template is shown.

  $examplePlay = '';

  if ( ! padTagParm ( 'skipPad' ) and ! $exampleOnlyResult and ! padTagParm ( 'onlyResult' ) and file_exists ( $examplePad ) )
    $examplePlay = padPlayLink ( padFileGet ( $examplePad ), '', file_exists ( $examplePhp ) ? padFileGet ( $examplePhp ) : '',
                                 (bool) padTagParm ( 'skipOpen' ) );

  return TRUE;

?>
