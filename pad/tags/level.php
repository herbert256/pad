<?php

  if ( $pTag[$p]== 'level')
    return pArr_to_html ( $pData[$pad-1] );
  else
    return pArr_to_html ( $pData[$pad-1] [$pKey[$pad-1]] );
 
  return $pReturn;

?>