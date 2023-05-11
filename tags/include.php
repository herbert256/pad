<?php
  
  $padIncPage = padPageGetName ();
  $padIncHtml = padApp . "pages/$padIncPage.html";
  $padIncPhp  = str_replace ( '.html', '.php', $padIncHtml);
  $padIncRet  = '';

  if ( padExists($padIncPhp) )
    $padIncRet.= "{call '$padIncPhp'}";    

  $padIncRet .= padFileGetContents ($padIncHtml);
      
  return $padIncRet;

?>