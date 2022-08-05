<?php

  if ( $pad_client_gzip 
           and 
       (!isset($_SERVER['HTTP_ACCEPT_ENCODING']) or strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip') === FALSE)
     )
    
    $pad_client_gzip = FALSE;

?>