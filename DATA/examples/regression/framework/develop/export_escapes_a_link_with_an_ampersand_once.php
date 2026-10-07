<?php

  // pad export writes the links it leaves pointing at a file as the attribute text they
  // were - an ampersand in a file name or a query stays &amp;, never &amp;amp; - in a
  // scratch PAD_HOME under DATA whose engine and _common are the real ones.

  $exHome = DATA . 'cli-export-amp-' . padRandomString ( 8 );
  $exOut  = "$exHome/out";

  mkdir ( "$exHome/apps/shop", 0755, TRUE );
  mkdir ( "$exHome/www/shop",  0755, TRUE );
  mkdir ( "$exHome/DATA",      0755, TRUE );

  symlink ( PAD,                   "$exHome/pad" );
  symlink ( rtrim ( COMMON, '/' ), "$exHome/apps/_common" );

  file_put_contents ( "$exHome/www/shop/index.php", "<?php include __DIR__ . '/../pad.php'; ?>\n" );
  mkdir ( "$exHome/apps/shop/_config" );
  file_put_contents ( "$exHome/apps/shop/_config/config.php", "<?php \$padCommon = FALSE; ?>\n" );
  file_put_contents ( "$exHome/apps/shop/index.pad", '<img src="img/a&amp;b.png" alt=""> <link rel="stylesheet" href="style.css?v=1&amp;t=2">' . "\n" );

  $exPhp  = ( PHP_SAPI === 'cli' or PHP_SAPI === 'cli-server' ) && PHP_BINARY !== '' ? PHP_BINARY : PHP_BINDIR . '/php';
  $exProc = proc_open ( [ $exPhp, dirname ( APPS ) . '/apps/cli/pad', 'export', 'shop', $exOut ],
                        [ 0 => [ 'file', '/dev/null', 'r' ], 1 => [ 'file', '/dev/null', 'w' ], 2 => [ 'file', '/dev/null', 'w' ] ],
                        $exPipes, NULL, array_merge ( getenv (), [ 'PAD_HOME' => $exHome ] ) );

  proc_close ( $exProc );

  preg_match_all ( '/(?:src|href)="[^"]*"/', (string) @file_get_contents ( "$exOut/index.html" ), $exLinks );

  // Each & as [and], so the answer reads the same whatever escapes it on the way out.

  $links = str_replace ( '&', '[and]', implode ( ' ', $exLinks [0] ) );

  padDeleteDataDir ( $exHome );

?>
