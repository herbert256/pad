<?php

  $examplePage       = $padParm ;
  $exampleTitle      = substr($examplePage, strrpos($examplePage, '/') + 1);
  $exampleFile       = APP . $examplePage;
  $exampleLayout     = padTagParm ( 'layout' , layout ("$exampleFile.pad") );
  $exampleOnlyResult = onlyResult ( "$exampleFile.pad" );

  $examplePhpGiven = padTagParm ( 'php' );
  $examplePadGiven = padTagParm ( 'pad' );

  $examplePhp      = ( $examplePhpGiven ) ? APP . $examplePhpGiven : "$exampleFile.php";
  $examplePad      = ( $examplePadGiven ) ? APP . $examplePadGiven : "$exampleFile.pad";

  $exampleSrcPhp   = ( file_exists($examplePhp) ) ? padColorsFile ($examplePhp) : '';
  $exampleSrcPad   = ( file_exists($examplePad) ) ? padColorsFile ($examplePad) : '';

  $exampleFilePhp  = ( file_exists($examplePhp) ) ? str_replace(APP, '', $examplePhp) : '';
  $exampleFilePad  = ( file_exists($examplePad) ) ? str_replace(APP, '', $examplePad) : '';

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