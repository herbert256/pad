<?php

  $pad_app_return = eval ($pad_parm);
        
  if ( $pad_app_return === NULL or $pad_app_return === FALSE)
    return '';
  
  return (string) $pad_app_return;

?>