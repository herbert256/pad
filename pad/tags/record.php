<?php

  if ($pTag[$p]== 'check' ) 
    return db ("$pTag[$p]$pParm[$p]") ? TRUE : FALSE;
  else                  
    return db ("$pTag[$p]$pParm[$p]");

?>