<?php

  // The directory of the terminal's jobs - and the debugger's state beside them - is this
  // user's own and closed to others, or nothing runs: its name in the shared temporary
  // directory is known to anyone who knows where the checkout is, and a directory another
  // user made there first, or a link they put there, would hold the scripts the terminal
  // runs and the debugger's token, theirs to read and change.

  $baseTry = function ( $base ) {
    try {
      editTermBase ( $base );
      return 'used';
    } catch ( RuntimeException $e ) {
      return 'refused';
    }
  };

  $baseTmp  = sys_get_temp_dir () . '/pad-edit-base-' . getmypid ();

  @mkdir ( "$baseTmp-open", 0777 );
  chmod ( "$baseTmp-open", 0777 );
  $baseOpen = $baseTry ( "$baseTmp-open/" );
  rmdir ( "$baseTmp-open" );

  mkdir ( "$baseTmp-real", 0700 );
  symlink ( "$baseTmp-real", "$baseTmp-link" );
  $baseLink = $baseTry ( "$baseTmp-link/" );
  unlink ( "$baseTmp-link" );
  rmdir ( "$baseTmp-real" );

  $baseOwn  = $baseTry ( "$baseTmp-own/" );
  $baseMode = sprintf ( '%o', fileperms ( "$baseTmp-own" ) & 0777 );
  rmdir ( "$baseTmp-own" );

  unset ( $baseTry, $baseTmp );

?>
