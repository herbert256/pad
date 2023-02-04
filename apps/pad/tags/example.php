<?php

  $exampleApp  = padTagParm ( 'app',  $app );
  $examplePage = padTagParm ( 'page', $padPrm [$pad] [1] );

  $exampleBase = PAD . $exampleApp . '/pages/';
  $exampleFile = $exampleBase . $examplePage;

  $examplePhp  = "$exampleFile.php";
  $exampleHtml = "$exampleFile.html";

  $exampleSrcPhp  = ( file_exists($examplePhp ) ) ? padColorsFile ($examplePhp )  : '';
  $exampleSrcHtml = ( file_exists($exampleHtml) ) ? padColorsFile ($exampleHtml) : '';

  $exampleFilePhp  = ( file_exists($examplePhp ) ) ? str_replace($exampleBase, '', $examplePhp  ) : '';
  $exampleFileHtml = ( file_exists($exampleHtml) ) ? str_replace($exampleBase, '', $exampleHtml ) : '';

  return TRUE;
   
?>