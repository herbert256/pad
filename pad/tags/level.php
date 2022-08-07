<?php

  if ( $pTag[$p]== 'level')
    return pArr_to_html ( $pData[$p-1] );
  else
    return pArr_to_html ( $pData[$p-1] [$pKey[$p-1]] );
 
  return $pReturn;

?>