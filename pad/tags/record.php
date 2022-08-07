<?php

  if ($pTag == 'check' ) 
    return db ("$pTag $pParm") ? TRUE : FALSE;
  else                  
    return db ("$pTag $pParm");

?>