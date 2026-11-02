<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  function padUnprotect ( $s ) { return $s; }
  function padProtect ( $s ) { return $s; }
  function padMakeSafe ( $s, $n ) { return substr ( $s, 0, $n ); }
  require PAD . 'subs/jsonld/_lib/jsonld.php';
  $errors = []; $report = function ( $s ) use ( &$errors ) { $errors [] = $s; };
  $row = [ 'name' => 'Repair </script>', 'provider' => 'Workshop', 'area' => [ 'Amsterdam', 'Utrecht' ], 'price' => '49.95', 'currency' => 'EUR' ];
  $object = padJsonLdMake ( 'service', [ $row ], [], NULL, $report );
  if ( $errors or $object ['@type'] !== 'Service' ) throw new Exception ( 'Mapping' );
  $html = padJsonLdScript ( $object );
  if ( substr_count ( $html, '</script>' ) !== 1 ) throw new Exception ( 'Script injection' );
  if ( json_decode ( substr ( $html, 35, -9 ), TRUE ) !== $object ) throw new Exception ( 'JSON round trip' );
if ( $object ['provider'] ['@type'] !== 'Organization' or $object ['offers'] ['@type'] !== 'Offer' or count ( $object ['areaServed'] ) !== 2 ) throw new Exception ( 'Nested mapping' );
  unset ( $row ['currency'] ); if ( padJsonLdMake ( 'service', [ $row ], [], NULL, $report ) !== FALSE or ! $errors ) throw new Exception ( 'Missing currency accepted' );
  echo "JSON-LD service: nested fields, validation, whitelist and script-safe JSON passed\n";
?>
