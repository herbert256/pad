<?php

  $padOS = strtolower ( substr ( php_uname ('s'), 0, 3 ) );

  if     ( $padOS == 'lin' ) $padHome = '/home/herbert/pad';
  elseif ( $padOS == 'dar' ) $padHome = '/Users/herbert/pad';
  elseif ( $padOS == 'win' ) $padHome = '/pad';
  else                       die ( "Unsuported OS: $padOS" );

  $padApps = "$padHome/apps/";
  $padData = "$padHome/DATA/";

  // Derive the app and the URL mount point of www/ from the entry script.
  // www/ may be served from the domain root (app at /demo/) or mounted under
  // a prefix (app at /pad/demo/) - SCRIPT_NAME tells us which entry ran.

  $padScriptName = str_replace ( '\\', '/', $_SERVER ['SCRIPT_NAME'] ?? '/index.php' );
  $padScriptDir  = str_replace ( '\\', '/', dirname ( $padScriptName ) );
  $padEntryDir   = dirname ( $_SERVER ['SCRIPT_FILENAME'] ?? __FILE__ );
  $padEntryApp   = basename ( $padScriptDir );

  if ( realpath ( $padEntryDir ) == __DIR__ ) {

    // Root entry (www/index.php): default app, www/ is mounted at the script's directory
    $padApp  = 'pad';
    $padRoot = ( $padScriptDir == '/' ) ? '/' : "$padScriptDir/";

  } elseif ( $padEntryApp and is_dir ( "$padApps$padEntryApp" ) ) {

    // App entry (www/<app>/index.php): app from directory name, www/ is one level up
    $padApp     = $padEntryApp;
    $padParent  = str_replace ( '\\', '/', dirname ( $padScriptDir ) );
    $padRoot    = ( $padParent == '/' ) ? '/' : "$padParent/";

  } else {

    $padApp  = 'pad';
    $padRoot = '/';

  }

  include "$padHome/pad/pad.php";

?>
