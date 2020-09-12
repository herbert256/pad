<?php

  if ($pad_tag == 'check' ) 
    return db ("$pad_tag $pad_parm") ? TRUE : FALSE;
  else                  
    return db ("$pad_tag $pad_parm");

?>