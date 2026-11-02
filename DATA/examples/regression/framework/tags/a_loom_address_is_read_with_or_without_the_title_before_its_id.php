<?php

  // The addresses of Loom - a shared recording with its title before the id or without it,
  // the embed form, the old useloom.com, a ?t= moment - are read as the 32 hex digits of the
  // recording and the moment; named, the id alone too. A shorter id, a page of Loom that is
  // no recording and a host that only looks like Loom's are none of its addresses.

  include_once PAD . 'subs/embeds/_lib/embed.php';

  $found = implode ( ' ', array_map ( function ( $url ) {
    $found = padEmbedFind ( $url );
    return $found ? "{$found [0]}:{$found [1] ['id']}" . ( $found [1] ['start'] ? "@{$found [1] ['start']}" : '' ) : 'none';
  }, [
    'https://www.loom.com/share/e5b8c04bca094dd8a5507925ab887002',
    'https://www.loom.com/share/How-to-file-an-expense-report-e5b8c04bca094dd8a5507925ab887002?sid=7f2a',
    'https://www.loom.com/embed/e5b8c04bca094dd8a5507925ab887002?t=1m20s',
    'https://www.useloom.com/share/e5b8c04bca094dd8a5507925ab887002',
    'https://www.loom.com/share/e5b8c04bca094dd8a55079',
    'https://www.loom.com/looms/videos',
    'https://loom.com.example.org/share/e5b8c04bca094dd8a5507925ab887002',
    'e5b8c04bca094dd8a5507925ab887002',
  ] ) );

  $named = padEmbedProvider ( 'loom' ) ['match'] ( 'e5b8c04bca094dd8a5507925ab887002', TRUE ) ['id'] ?? 'none';

?>
