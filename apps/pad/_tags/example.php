<?php

  $examplePage       = $padParm ;
  $exampleTitle      = substr($examplePage, strrpos($examplePage, '/') + 1);
  $exampleFile       = '/app/' . $examplePage;
  $exampleLayout     = padTagParm ( 'layout' , layout ("$exampleFile.pad") );
  $exampleOnlyResult = onlyResult ( "$exampleFile.pad" );

  $examplePhpGiven = padTagParm ( 'php' );
  $examplePadGiven = padTagParm ( 'pad' );

  $examplePhp      = ( $examplePhpGiven ) ? '/app/' . $examplePhpGiven : "$exampleFile.php";
  $examplePad      = ( $examplePadGiven ) ? '/app/' . $examplePadGiven : "$exampleFile.pad";

  $exampleSrcPhp   = ( file_exists($examplePhp) ) ? padColorsFile ($examplePhp) : '';
  $exampleSrcPad   = ( file_exists($examplePad) ) ? padColorsFile ($examplePad) : '';

  $exampleFilePhp  = ( file_exists($examplePhp) ) ? str_replace('/app/', '', $examplePhp) : '';
  $exampleFilePad  = ( file_exists($examplePad) ) ? str_replace('/app/', '', $examplePad) : '';

  if ( padTagParm ( 'skipWhenEmpty' ) ) {
    if ( ! padFileGetContents ( $examplePhp ) ) $padPrm [$pad] ['skipPhp'] = TRUE;
    if ( ! padFileGetContents ( $examplePad ) ) $padPrm [$pad] ['skipPad'] = TRUE;
  }

  return TRUE;
   
?>