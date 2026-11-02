<?php
  define ( 'PAD', dirname ( __DIR__, 3 ) . '/pad/' );
  function padDirList ( $p ) { return scandir ( $p ); }
  require PAD . 'lib/subsystem.php'; require PAD . 'lib/embed.php'; require PAD . 'subs/embeds/_lib/embed.php';
  $type = padEmbedProvider ( 'googleslides' );
  $parts = $type ['match'] ( 'https://docs.google.com/presentation/d/e/2PACX-1vPublishedExampleIdentifier/pub?start=true', TRUE );
  if ( ! $parts or padEmbedFind ( 'https://docs.google.com/presentation/d/e/2PACX-1vPublishedExampleIdentifier/pub?start=true' ) [0] !== 'googleslides' ) throw new Exception ( 'Auto detection' );
  $embed = $type ['embed'] ( $parts, [] );
  foreach ( ['https://docs.google.com/presentation/d/private-edit-id/edit', 'https://docs.google.com.evil.test/presentation/d/e/2PACX-1vPublishedExampleIdentifier/pub', 'https://evil@docs.google.com/presentation/d/e/2PACX-1vPublishedExampleIdentifier/pub'] as $url ) if ( $type ['match'] ( $url, TRUE ) !== NULL ) throw new Exception ( 'Invalid URL accepted' );
  if ( str_contains ( $embed ['src'], 'start=true' ) ) throw new Exception ( 'Autoplay query leaked' );
  echo "googleslides: URL selection/rejection, auto detection and viewer construction passed\n";
?>
