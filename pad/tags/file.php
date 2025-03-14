<?php

  if ( padStartAndClose ('end') )
    return TRUE;

  $padFile = $padParm ;

  padFilePutContents ( $padFile , padUnEscape ( trim($padContent) ) );

  $padContent = '';

  return TRUE;
 
?>