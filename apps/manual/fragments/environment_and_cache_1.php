<?php

  // apps/manual/.env holds MANUAL_SITE_NAME,
  // MANUAL_PAGE_SIZE and MANUAL_DEBUG.

  $site  = padEnv ( 'MANUAL_SITE_NAME', 'My site' );
  $rows  = padEnv ( 'MANUAL_PAGE_SIZE', 25 );
  $debug = padEnv ( 'MANUAL_DEBUG', TRUE ) ? 'on' : 'off';
  $maps  = padEnv ( 'MANUAL_MAPS_KEY', 'not set' );

?>
