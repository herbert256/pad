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

  if     ( strpos ( $_SERVER ['HTTP_USER_AGENT'], 'Linux'     ) ) $padHome = '/home/herbert';
  elseif ( strpos ( $_SERVER ['HTTP_USER_AGENT'], 'Windows'   ) ) $padHome = 'C:\Users\herbe';
  elseif ( strpos ( $_SERVER ['HTTP_USER_AGENT'], 'Macintosh' ) ) $padHome = '/Users/herbert';
  elseif ( strpos ( $_SERVER ['HTTP_USER_AGENT'], 'CrOS'      ) ) $padHome = '/home/herbert';
  else                                                            $padHome = '/oops/not/home';

  $padHome = substr ( $_SERVER ['DOCUMENT_ROOT'], 0, strpos ( $_SERVER ['DOCUMENT_ROOT'], '/pad/www') );

  define ( 'pad',     "$padHome/pad/"     ); // Home of PAD itself
  define ( 'padApp',  "$padHome/pad/app/" ); // The PAD application files
  define ( 'padData', "$padHome/data/"    ); // Data locaction, used for logs/cache/errors/etc.
  
  include pad . 'start.php';

?>