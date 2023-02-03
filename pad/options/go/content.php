<?php

  if ( isset ( $padContentStore [$padContentGo] ) )
    return $padContentStore [$padContentGo];

  if ( ! padIsContentFile ($padContentGo) )
    return padMakeContent ( $padContentGo );

  $padContentGo = APP . "content/$padContentGo";

  if ( substr($padContentGo , -4) == '.php' )
    return "{call '$padContentGo'}";    

  return padGetHtml ( "$padContentGo.html" , TRUE);

?>