<?php

  //  =============================================================================
  //  PAD - (P)HP (A)pplication (D)river
  //  (c) 2004-2023 by Herbert Groot Jebbink - herbert@groot.jebbink.nl
  //  =============================================================================
  //
  //  This is the PAD startup file for CLI
  //
  //  =============================================================================

  define ( 'pad',     "../"                   );   // Home of PAD itself
  define ( 'padApp',  "./app/"               );   // The application files
  define ( 'padData', "/Users/herbert/data/" );   // Data locaction, used for logs/cache/errors/etc.
  
  include pad . 'start.php';

?>