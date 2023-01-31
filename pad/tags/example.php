<?php

  $padExampleApp  = padTagParm ( 'app',  $app );
  $padExamplePage = padTagParm ( 'page', $padPrm [$pad] [1] );

  $padExampleBase = PAD . $padExampleApp . '/pages/';
  $padExampleFile = $padExampleBase . $padExamplePage;

  $padExamplePhp  = "$padExampleFile.php";
  $padExampleHtml = "$padExampleFile.html";

  $padExampleSrcPhp  = ( file_exists($padExamplePhp ) ) ? padColorsFile ($padExamplePhp )  : '';
  $padExampleSrcHtml = ( file_exists($padExampleHtml) ) ? padColorsFile ($padExampleHtml) : '';

  $padExampleFilePhp  = ( file_exists($padExamplePhp ) ) ? str_replace($padExampleBase, '', $padExamplePhp  ) : '';
  $padExampleFileHtml = ( file_exists($padExampleHtml) ) ? str_replace($padExampleBase, '', $padExampleHtml ) : '';

  return TRUE;
   
?>