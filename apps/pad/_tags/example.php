<?php

  $examplePage       = padPageGetName ();
  $exampleTitle      = substr($examplePage, strrpos($examplePage, '/') + 1);
  $exampleFile       = padApp . $examplePage;
  $exampleType       = padTagParm ( 'type', 'sandbox' );
  $exampleLayout     = padTagParm ( 'layout' , layout ("$exampleFile.pad") );
  $exampleOnlyResult = onlyResult ( "$exampleFile.pad" );

  $examplePhpGiven  = padTagParm ( 'php' );
  $examplePadGiven = padTagParm ( 'pad' );

  $examplePhp       = ( $examplePhpGiven )  ? padApp . $examplePhpGiven  : "$exampleFile.php";
  $examplePad      = ( $examplePadGiven ) ? padApp . $examplePadGiven : "$exampleFile.pad";

  $exampleSrcPhp    = ( padExists($examplePhp ) ) ? padColorsFile ($examplePhp ) : '';
  $exampleSrcPad   = ( padExists($examplePad) ) ? padColorsFile ($examplePad) : '';

  $exampleFilePhp   = ( padExists($examplePhp ) ) ? str_replace(padApp, '', $examplePhp  ) : '';
  $exampleFilePad  = ( padExists($examplePad) ) ? str_replace(padApp, '', $examplePad ) : '';

  return TRUE;
   
?>