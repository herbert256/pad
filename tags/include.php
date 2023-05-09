<?php
  
  $padIncPage = padTagParm ( 'page', $padOpt [$pad] [1] );
  $padIncHtml = padApp . "pages/$padIncPage.html";
  $padIncPhp  = str_replace ( '.html', '.php', $padIncHtml);
  $padIncRet  = '';

  if ( padExists($padIncPhp) )
    $padIncRet.= "{call '$padIncPhp'}";    

  $padIncRet .= padFileGetContents ($padIncHtml);
      
  return $padIncRet;

?>