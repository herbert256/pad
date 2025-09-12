<?php

  $links = links ( APP . "$dir" ) ;

  if ( ! isset ( $next ) or ! $next )
    $next = $default;

  $title .= " - $next"

?>