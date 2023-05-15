<?php

  $skipNames = FALSE;
  
  $examplePage  = padPageGetName ();
  $exampleTitle = substr($examplePage, strrpos($examplePage, '/'));
  $exampleTitle = padTagParm ( 'title', $exampleTitle );

  if ( isset ( $padPrm [$pad] ['onlyResult'] ) )
    return TRUE;

  $exampleFile = padApp . $examplePage;
 
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