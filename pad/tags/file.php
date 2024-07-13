<?php

  if ( padStartAndClose ('end') )
    return TRUE;

  $padFile = $padParm ;

  padFilePutContents ( $padFile , padUnEscape ( trim($padContent) ) );

  $padContent = "<hr>$padFile<hr><pre>" . htmlentities ( trim($padContent) ) . "</pre><br>";

  return TRUE;
 
?>