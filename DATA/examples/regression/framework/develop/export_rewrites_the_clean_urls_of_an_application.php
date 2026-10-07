<?php

  // pad export of an application with $padCleanUrls: its links name a page by the path -
  // /shop/about, /shop/about&x=1 - and each becomes the page.html it was exported to, in a
  // scratch PAD_HOME under DATA whose engine and _common are the real ones.

  $exHome = DATA . 'cli-export-clean-' . padRandomString ( 8 );
  $exOut  = "$exHome/out";

  mkdir ( "$exHome/apps/shop", 0755, TRUE );
  mkdir ( "$exHome/www/shop",  0755, TRUE );
  mkdir ( "$exHome/DATA",      0755, TRUE );

  symlink ( PAD,                   "$exHome/pad" );
  symlink ( rtrim ( COMMON, '/' ), "$exHome/apps/_common" );

  file_put_contents ( "$exHome/www/shop/index.php", "<?php include __DIR__ . '/../pad.php'; ?>\n" );
  mkdir ( "$exHome/apps/shop/_config" );
  file_put_contents ( "$exHome/apps/shop/_config/config.php", "<?php \$padCommon = FALSE; \$padCleanUrls = TRUE; ?>\n" );
  file_put_contents ( "$exHome/apps/shop/index.pad", '<a href="{$padGo}about">a</a> <a href="{$padGo}about&x=1">b</a> <a href="{$padGo}">c</a>' . "\n" );
  file_put_contents ( "$exHome/apps/shop/about.pad", "<p>about</p>\n" );

  $exPhp  = ( PHP_SAPI === 'cli' or PHP_SAPI === 'cli-server' ) && PHP_BINARY !== '' ? PHP_BINARY : PHP_BINDIR . '/php';
  $exProc = proc_open ( [ $exPhp, dirname ( APPS ) . '/apps/cli/pad', 'export', 'shop', $exOut ],
                        [ 0 => [ 'file', '/dev/null', 'r' ], 1 => [ 'file', '/dev/null', 'w' ], 2 => [ 'file', '/dev/null', 'w' ] ],
                        $exPipes, NULL, array_merge ( getenv (), [ 'PAD_HOME' => $exHome ] ) );

  proc_close ( $exProc );

  preg_match_all ( '/href="[^"]*"/', (string) @file_get_contents ( "$exOut/index.html" ), $exLinks );

  $links = implode ( ' ', $exLinks [0] );

  padDeleteDataDir ( $exHome );

?>
