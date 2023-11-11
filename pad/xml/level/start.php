<?php
  
  $padXmlOcc [$pad] = '';

  if ( $pad == 0 )
    return;

  $padXmlTag [$pad] = include pad . 'xml/level/tag.php';

  if ( str_starts_with ( $padTag [$pad], 'entry') ) 
    $padXmlParms = include pad . 'xml/level/entry.php';
  else
    $padXmlParms = include pad . 'xml/level/parms.php';

  padXmlWriteOpen ( $padXmlTag [$pad], $padXmlParms );

?>