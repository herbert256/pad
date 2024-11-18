<?php

  //  =============================================================================
  //  PAD - (P)HP (A)pplication (D)river
  //  (c) 2004-2024 by Herbert Groot Jebbink - herbert@groot.jebbink.nl
  //  =============================================================================
  //
  //  This is the PAD startup file for HTTP
  //
  //  Only this file must be inside the webservers htdocs directory,
  //  all other PAD files must be stored *OUTSIDE* the webservers htdocs directory.
  //
  //  =============================================================================

  $padOS = substr ( strtolower ( php_uname ('s') ), 0, 3);

  if     ( $padOS == 'dar' ) $padHome = '/Users/herbert/pad';
  elseif ( $padOS == 'win' ) $padHome = '/pad';
  elseif ( $padOS == 'lin' ) $padHome = '/home/herbert/pad';

  define ( 'APP', "$padHome/apps/$app/" );
  define ( 'PAD', "$padHome/pad/"  );
  define ( 'DAT', "$padHome/data/" );

  include PAD . 'pad.php';

?>