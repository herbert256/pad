<?php

  //  =============================================================================
  //  PAD - (P)HP (A)pplication (D)river
  //  (c) 2004-2023 by Herbert Groot Jebbink - herbert@groot.jebbink.nl
  //  =============================================================================
  //
  //  This is the PAD startup file for CLI
  // 
  //
  //  usage: php pad.php <page>
  //
  //  <page> is without '.php' or '.pad' extension
  //
  //  =============================================================================

  define ( 'pad',     "/workspaces/pad/pad/"    );   // Home of PAD itself
  define ( 'padApp',  "/workspaces/pad/github/" );   // The application files
  define ( 'padData', "/workspaces/data/"       );   // Data locaction, used for logs/cache/errors/etc.
  
  include pad . 'pad.php';

?>