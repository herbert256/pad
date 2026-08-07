<?php

  $exampleApp        = padTagParm ( 'app' );
  $exampleDir        = ( $exampleApp ) ? APPS . "$exampleApp/" : APP;

  $examplePage       = $padParm ;

  // The title is the last part of the name. strrpos answers FALSE when the name has no / at
  // all, and FALSE + 1 is 1, so an example named without a directory - {example 'offices'} -
  // used to lose its first letter and come out as "ffices".

  $exampleSlash      = strrpos ( $examplePage, '/' );
  $exampleTitle      = ( $exampleSlash === FALSE ) ? $examplePage : substr ( $examplePage, $exampleSlash + 1 );
  $exampleFile       = $exampleDir . $examplePage;
  $exampleLayout     = padTagParm ( 'layout' , layout ("$exampleFile.pad") );
  $exampleOnlyResult = onlyResult ( "$exampleFile.pad" );

  $examplePhpGiven = padTagParm ( 'php' );
  $examplePadGiven = padTagParm ( 'pad' );

  $examplePhp = $examplePad = $exampleDir;

  $examplePhp      .= ( $examplePhpGiven ) ? $examplePhpGiven : "$examplePage.php";
  $examplePad      .= ( $examplePadGiven ) ? $examplePadGiven : "$examplePage.pad";

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

  return TRUE;

?>