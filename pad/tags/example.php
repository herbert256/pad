<?php

  $padExampleApp  = padTagParm ( 'app',  $app );
  $padExamplePage = padTagParm ( 'page', $padPrm [$pad] [1] );

  $padExampleBase = PAD . $padExampleApp . '/pages/';
  $padExampleFile = $padExampleBase . $padExamplePage;
  $padExamplePhp  = "$padExampleFile.php";
  $padExampleHtml = "$padExampleFile.html";

  $php    = ( file_exists($padExamplePhp ) ) ? padColorsFile ($padExamplePhp )  : '';
  $html   = ( file_exists($padExampleHtml) ) ? padColorsFile ($padExampleHtml) : '';

  $php_file  = ( file_exists($padExamplePhp ) ) ? str_replace($padExampleFile, '', $padExamplePhp  ) : '';
  $html_file = ( file_exists($padExampleHtml) ) ? str_replace($padExampleFile, '', $padExampleHtml ) : '';

  return TRUE;
   
?>