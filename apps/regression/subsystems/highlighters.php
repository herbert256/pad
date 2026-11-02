<?php
  define ( 'PAD', dirname ( __DIR__, 3 ) . '/pad/' );
  function padDirList ( $path ) { return scandir ( $path ); }
  function padUnprotectQuotes ( $s ) { return $s; }
  require PAD . 'lib/subsystem.php';
  require PAD . 'lib/highlight.php';
  set_error_handler ( function ( $n, $s ) { throw new Exception ( $s ); } );
  foreach ( json_decode ( file_get_contents ( __DIR__ . '/highlighters-compat.json' ), TRUE ) as $case )
    if ( hash ( 'sha256', padHighlightTokens ( $case ['source'], $case ['language'] ) ) !== $case ['sha256'] )
      throw new Exception ( 'Changed existing output: ' . $case ['language'] );
  $samples = [ 'python' => "def hello():\n  return True # comment", 'go' => 'package main; func main() {}',
    'rust' => 'pub fn main() { let mut x = 1; }', 'typescript' => 'interface User { readonly name: string; }',
    'diff' => "--- old\n+++ new\n@@ -1 +1 @@\n-old\n+new" ];
  foreach ( $samples as $kind => $source ) {
    $html = padHighlightTokens ( $source, $kind );
    if ( ! str_contains ( $html, 'class="hl-' ) or html_entity_decode ( strip_tags ( $html ), ENT_QUOTES, 'UTF-8' ) !== $source ) throw new Exception ( $kind );
  }
  if ( ! padHighlightKnown ( 'py' ) or padHighlightLanguage ( 'ts' ) !== 'typescript' or padHighlightKnown ( '../php' ) ) throw new Exception ( 'aliases' );
  foreach ( padKinds ( 'highlighters' ) as $kind ) {
    foreach ( [ '<script>alert("x")</script>', '"' . str_repeat ( 'a', 30000 ) . '"', str_repeat ( '{-- ', 10000 ) ] as $source ) {
      $html = padHighlightTokens ( $source, $kind );
      if ( html_entity_decode ( strip_tags ( $html ), ENT_QUOTES, 'UTF-8' ) !== $source ) throw new Exception ( "Escaping $kind" );
    }
  }
  echo "Highlighters: existing output, new grammars, aliases, escaping and long input passed\n";
?>
