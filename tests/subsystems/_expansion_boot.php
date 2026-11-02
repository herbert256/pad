<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  function padDirList ( $path ) { return scandir ( $path ); }
  function padError ( $message ) { throw new InvalidArgumentException ( $message ); }
  require PAD . 'lib/subsystem.php';
  function subCheck ( $value, $message = 'fixture failed' ) { if ( ! $value ) throw new RuntimeException ( $message ); }
  function subNear ( $a, $b, $epsilon = 1e-10 ) { subCheck ( is_numeric ( $a ) && abs ( $a - $b ) <= $epsilon * max ( 1, abs ( $b ) ), "expected $b, got " . var_export ( $a, TRUE ) ); }
  function subReject ( $call ) { try { $call (); } catch ( InvalidArgumentException $e ) { return; } throw new RuntimeException ( 'invalid input accepted' ); }
?>
