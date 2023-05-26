<?php

  $skipNames = FALSE;
  
  $exampleType   = 'sandbox';

  $examplePage   = padPageGetName ();
  $exampleTitle  = substr($examplePage, strrpos($examplePage, '/') + 1);
  $exampleLayout = 'horizontal';

  $exampleType = padTagParm ( 'type' );
  
  if ( ! $exampleType )
    if     ( strpos($examplePage, 'restart')  !== FALSE ) $exampleType = 'ajax';
    elseif ( strpos($examplePage, 'redirect') !== FALSE ) $exampleType = 'ajax';
    else                                                  $exampleType = 'sandbox';

  if ( isset ( $padPrm [$pad] ['onlyResult'] ) )
    return TRUE;

  $exampleFile = padApp . $examplePage;
 
  $examplePhpGiven  = padTagParm ( 'php' );
  $exampleHtmlGiven = padTagParm ( 'html' );

  $examplePhp  = ( $examplePhpGiven )  ? padApp . $examplePhpGiven  : "$exampleFile.php";
  $exampleHtml = ( $exampleHtmlGiven ) ? padApp . $exampleHtmlGiven : "$exampleFile.html";

  $exampleSrcPhp  = ( padExists($examplePhp ) ) ? padColorsFile ($examplePhp ) : '';
  $exampleSrcHtml = ( padExists($exampleHtml) ) ? padColorsFile ($exampleHtml) : '';

  $exampleLayout = ( strpos($exampleSrcHtml, '&lt;padVertical&gt;' ) ) ? 'vertical' : $exampleLayout;
  $exampleLayout = ( padTagParm ( 'vertical')                        ) ? 'vertical' : $exampleLayout;

  $exampleSrcHtml = str_replace ( '&lt;padVertical&gt;', '', $exampleSrcHtml);    

  $exampleFilePhp  = ( padExists($examplePhp ) ) ? str_replace(padApp, '', $examplePhp  ) : '';
  $exampleFileHtml = ( padExists($exampleHtml) ) ? str_replace(padApp, '', $exampleHtml ) : '';

  return TRUE;
   
?>