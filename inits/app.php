<?php

  $app = $app ?? $_REQUEST['app'] ?? 'pad';
  
  if ( ! defined ('APP') )
    define ( 'APP', APPS . "$app/" );

?>