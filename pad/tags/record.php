<?php

  if ($pTag [$p] == 'check' ) 
    return db ("$pTag [$p]$pPrm [$p]") ? TRUE : FALSE;
  else                  
    return db ("$pTag [$p]$pPrm [$p]");

?>