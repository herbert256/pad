<?php

  $app = $app ?? $_REQUEST['app'] ?? 'pad';
  
  if ( ! defined ('APP') )
    define ( 'APP', PAD. "$app/" );
    define ( 'APP', PAD. "$app/" );

?>