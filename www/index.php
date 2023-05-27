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
  $padOS    = strtolower ( PHP_OS );

  if     ( $padUname == 'linux'             ) $padHome = '/home/herbert';
  elseif ( $padUname == 'windows'           ) $padHome = '/xampp';
  elseif ( $padUname == 'darwin'            ) $padHome = '/Users/herbert';
  elseif ( $padUname == 'cros'              ) $padHome = '/home/herbert';
  elseif ( PHP_OS    == 'linux'             ) $padHome = '/home/herbert';
  elseif ( PHP_OS    == 'windows'           ) $padHome = '/xampp';
  elseif ( PHP_OS    == 'darwin'            ) $padHome = '/Users/herbert';
  elseif ( PHP_OS    == 'cros'              ) $padHome = '/home/herbert';
  elseif ( str_starts_with (PHP_OS, 'win' ) ) $padHome = '/xampp';
  else                                        $padHome = '/oops/pad/not/found';

  define ( 'pad',     "$padHome/pad/"     ); // Home of PAD itself
  define ( 'padApp',  "$padHome/pad/app/" ); // The PAD application files
  define ( 'padData', "$padHome/data/"    ); // Data locaction, used for logs/cache/errors/etc.
  
  set_include_path ( pad );

  include 'start.php';

?>