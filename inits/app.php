<?php

  $padApp = $padApp ?? $_REQUEST['app'] ?? $_REQUEST['padApp'] ?? 'pad';
  
  if ( ! defined ('padApp') )
    define ( 'padApp', padApps . "$padApp/" );

?>