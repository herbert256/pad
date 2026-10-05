<?php

  $padOS = strtolower ( substr ( php_uname ('s'), 0, 3 ) );

  if     ( $padOS == 'lin' ) $padHome = '/home/herbert/pad';
  elseif ( $padOS == 'dar' ) $padHome = '/Users/herbert/pad';
  elseif ( $padOS == 'win' ) $padHome = '/pad';
  else                       die ( 'Unsuported OS: ' . php_uname ('s') );

  // A second checkout - a git worktree with its own php -S server - says where it lives in
  // the PAD_HOME environment variable; without it every entry point would run this
  // machine's main tree. The web server never sets it, so nothing changes there.

  if ( getenv ( 'PAD_HOME' ) )
    $padHome = rtrim ( getenv ( 'PAD_HOME' ), '/' );

?>
