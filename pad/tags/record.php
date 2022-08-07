<?php

  if ($pTag == 'check' ) 
    return db ("$pTag $pParm [$p]") ? TRUE : FALSE;
  else                  
    return db ("$pTag $pParm [$p]");

?>