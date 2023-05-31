<?php

  $examplePage   = padPageGetName ();
  $exampleTitle  = substr($examplePage, strrpos($examplePage, '/') + 1);
  $exampleLayout = 'horizontal';
  $exampleFile   = padApp . $examplePage;

  if     ( strpos($examplePage, 'restart')  !== FALSE ) $exampleType = 'ajax';
  elseif ( strpos($examplePage, 'redirect') !== FALSE ) $exampleType = 'ajax';
  else                                                  $exampleType = padTagParm ( 'type', 'sandbox' );

  $exampleLayout     = padTagParm ( 'layout' , layout ("$exampleFile.html") );
  $exampleOnlyResult = onlyResult ( "$exampleFile.html" );

  $examplePhpGiven  = padTagParm ( 'php' );
  $exampleHtmlGiven = padTagParm ( 'html' );

  $examplePhp  = ( $examplePhpGiven )  ? padApp . $examplePhpGiven  : "$exampleFile.php";
  $exampleHtml = ( $exampleHtmlGiven ) ? padApp . $exampleHtmlGiven : "$exampleFile.html";

  $exampleSrcPhp  = ( padExists($examplePhp ) ) ? padColorsFile ($examplePhp ) : '';
  $exampleSrcHtml = ( padExists($exampleHtml) ) ? padColorsFile ($exampleHtml) : '';

  $exampleFilePhp  = ( padExists($examplePhp ) ) ? str_replace(padApp, '', $examplePhp  ) : '';
  $exampleFileHtml = ( padExists($exampleHtml) ) ? str_replace(padApp, '', $exampleHtml ) : '';

  return TRUE;
   
?>