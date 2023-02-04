<?php

  if ( ! padIsContentFile ($padContentGo) )
    return padMakeContent (   $padContentGo );

  $padContentGo = APP . "content/$padContentGo";

  if ( substr($padContentGo , -4) == '.php' )
    return "{call '$padContentGo'}";    

  if ( substr($padContentGo , -5) == '.html' )
    return padFileGetContents ($padContentGo);    

  return padGetHtml ( "$padContentGo.html" , TRUE);

?>