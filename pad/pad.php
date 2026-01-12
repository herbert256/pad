<?php

  if ( ! isset ( $padMicro ) ) $padMicro = microtime ( TRUE );
  if ( ! isset ( $padHR )    ) $padHR    = hrtime    ( TRUE );

  if ( ! isset ( $padApps ) ) die ( 'Variable $padApps must be set before calling this script' );
  if ( ! isset ( $padApp  ) ) die ( 'Variable $padApp must be set before calling this script' );
  if ( ! isset ( $padData ) ) die ( 'Variable $padData must be set before calling this script' );

  if ( ! str_ends_with ( $padApps, '/' ) ) $padApps .= '/';
  if ( ! str_ends_with ( $padData, '/' ) ) $padData .= '/';
 
  define ( 'PAD',    dirname ( __FILE__ ) . '/' );
  define ( 'APP',    $padApps . $padApp . '/'   );
  define ( 'DAT',    $padData                   );
  define ( 'APPS',   $padApps                   );
  define ( 'DATA',   $padData                   );
  define ( 'COMMON', $padApps . '_common/'      );

  if ( ! file_exists (APP) or ! is_dir (APP) ) die ( "Application directory not found: " . APP );

  chdir            ( APP );
  set_include_path ( APP );

  include PAD . 'start/enter/pad.php';

?>