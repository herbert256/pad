<?php

  if ( $padClientGzip 
           and 
       (!isset($_SERVER['HTTP_ACCEPT_ENCODING']) or strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip') === FALSE)
     )
    
    $padClientGzip = FALSE;

?>