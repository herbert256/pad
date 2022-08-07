<?php

  if ( $pClient_gzip 
           and 
       (!isset($_SERVER['HTTP_ACCEPT_ENCODING']) or strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip') === FALSE)
     )
    
    $pClient_gzip = FALSE;

?>