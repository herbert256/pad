<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  function padUnprotect ( $s ) { return $s; }
  function padProtect ( $s ) { return $s; }
  function padMakeSafe ( $s, $n ) { return substr ( $s, 0, $n ); }
  require PAD . 'subs/jsonld/_lib/jsonld.php';
  $errors = []; $report = function ( $s ) use ( &$errors ) { $errors [] = $s; };
  $row = [ 'name' => 'Temperatures </script>', 'description' => 'Monthly readings', 'creator' => 'Observatory', 'license' => 'https://example.org/license', 'distribution' => [ [ 'contentUrl' => 'https://example.org/data.csv', 'encodingFormat' => 'text/csv', 'secret' => 'never-export' ] ] ];
  $object = padJsonLdMake ( 'dataset', [ $row ], [], NULL, $report );
  if ( $errors or $object ['@type'] !== 'Dataset' ) throw new Exception ( 'Mapping' );
  $html = padJsonLdScript ( $object );
  if ( substr_count ( $html, '</script>' ) !== 1 ) throw new Exception ( 'Script injection' );
  if ( json_decode ( substr ( $html, 35, -9 ), TRUE ) !== $object ) throw new Exception ( 'JSON round trip' );
if ( $object ['creator'] ['@type'] !== 'Organization' or $object ['distribution'] [0] ['@type'] !== 'DataDownload' or isset ( $object ['distribution'] [0] ['secret'] ) ) throw new Exception ( 'Nested mapping' );
  $bad = padJsonLdDownloads ( [ [ 'contentUrl' => 'javascript:alert(1)' ] ], 'distribution', 'dataset', $report );
  if ( $bad !== NULL or ! $errors ) throw new Exception ( 'Unsafe URL accepted' );
  echo "JSON-LD dataset: nested fields, validation, whitelist and script-safe JSON passed\n";
?>
