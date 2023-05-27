<?php

  //  ============================================================================
  //  PAD - (P)HP (A)pplication (D)river
  //  (c) 2004-2023 by Herbert Groot Jebbink - herbert@groot.jebbink.nl
  //  ============================================================================
  //
  //  This is the PAD startup file, the first file that becomes active.
  //
  //  Only this file must be located inside the webservers htdocs directory,
  //  all other PAD files must be stored *OUTSIDE* the webservers htdocs directory.
  //
  //  ============================================================================

  $padUname = strtolower ( php_uname('s') );

  if     ( str_starts_with ( $padUname, 'linux'  ) ) $padHome = '/home/herbert';
  elseif ( str_starts_with ( $padUname, 'darwin' ) ) $padHome = '/Users/herbert';
  elseif ( str_starts_with ( $padUname, 'win'    ) ) $padHome = '/xampp';
  else                                               $padHome = '/oops/not/found';

  define ( 'pad',     "$padHome/pad/"     ); // Home of PAD itself
  define ( 'padApp',  "$padHome/pad/app/" ); // The PAD application files
  define ( 'padData', "$padHome/data/"    ); // Data locaction, used for logs/cache/errors/etc.
  
  set_include_path ( pad );

  include 'start.php';

?>