<?php

  if ( padStartAndClose ('end') )
    return TRUE;

  $padFile = $padOpt [$pad] [1] ;

  padFilePutContents ( $padFile , trim($padContent) );

  $padContent = "<hr>$padFile<hr><pre>" . htmlentities ( trim($padContent) ) . "</pre><br>";

  return TRUE;
 
?>