<?php
  define ( 'PAD', dirname ( __DIR__, 3 ) . '/pad/' );
  function padUnprotect ( $s ) { return $s; }
  function padProtect ( $s ) { return $s; }
  function padMakeSafe ( $s, $n ) { return substr ( $s, 0, $n ); }
  require PAD . 'subs/jsonld/_lib/jsonld.php';
  $errors = []; $report = function ( $s ) use ( &$errors ) { $errors [] = $s; };
  $row = [ 'name' => 'A book </script>', 'author' => [ 'Ann', 'Bo' ], 'isbn' => '978-0-306-40615-7', 'language' => 'en', 'edition' => 'Second', 'pages' => 300 ];
  $object = padJsonLdMake ( 'book', [ $row ], [], NULL, $report );
  if ( $errors or $object ['@type'] !== 'Book' ) throw new Exception ( 'Mapping' );
  $html = padJsonLdScript ( $object );
  if ( substr_count ( $html, '</script>' ) !== 1 ) throw new Exception ( 'Script injection' );
  if ( json_decode ( substr ( $html, 35, -9 ), TRUE ) !== $object ) throw new Exception ( 'JSON round trip' );
if ( $object ['isbn'] !== '9780306406157' or $object ['author'] [0] ['@type'] !== 'Person' or $object ['bookEdition'] !== 'Second' ) throw new Exception ( 'Book mapping' );
  $row ['isbn'] = '9780306406158'; $bad = padJsonLdMake ( 'book', [ $row ], [], NULL, $report );
  if ( isset ( $bad ['isbn'] ) or ! $errors ) throw new Exception ( 'Bad ISBN accepted' );
  echo "JSON-LD book: nested fields, validation, whitelist and script-safe JSON passed\n";
?>
