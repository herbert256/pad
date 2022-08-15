<?php

  if ( $padTag [$pad] == 'level')
    return pArr_to_html ( $padData[$pad-1] );
  else
    return pArr_to_html ( $padData[$pad-1] [$padKey[$pad-1]] );
 
  return $padReturn;

?>