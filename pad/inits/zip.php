<?php

  if ( $padClient_gzip 
           and 
       (!isset($_SERVER['HTTP_ACCEPT_ENCODING']) or strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip') === FALSE)
     )
    
    $padClient_gzip = FALSE;

?>