<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
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
  foreach ( padKinds ( 'widgets/highlighters' ) as $kind ) {
    foreach ( [ '<script>alert("x")</script>', '"' . str_repeat ( 'a', 30000 ) . '"', str_repeat ( '{-- ', 10000 ) ] as $source ) {
      $html = padHighlightTokens ( $source, $kind );
      if ( html_entity_decode ( strip_tags ( $html ), ENT_QUOTES, 'UTF-8' ) !== $source ) throw new Exception ( "Escaping $kind" );
    }
  }

  $go = "var a = `x\\`\nvar b = `y`";
  $html = padHighlightTokens ( $go, 'go' );
  if ( substr_count ( $html, 'class="hl-kwd">var<' ) !== 2 or substr_count ( $html, 'class="hl-str"' ) !== 2 ) throw new Exception ( 'Go raw string termination' );
  $rust = "fn borrow<'a>(s: &'a str) -> &'a str { s }";
  $html = padHighlightTokens ( $rust, 'rust' );
  if ( str_contains ( $html, 'class="hl-str"' ) or substr_count ( $html, 'class="hl-var"' ) !== 3 ) throw new Exception ( 'Rust lifetimes' );
  foreach ( [ "'a'", "'é'", "'\\n'", 'r##"a "quoted" \\"##', 'br#"raw"#', "\"multi\nline\"" ] as $literal ) {
    $html = padHighlightTokens ( $literal, 'rust' );
    if ( $html !== '<span class="hl-str">' . padHighlightEscape ( $literal ) . '</span>' ) throw new Exception ( 'Rust literal: ' . $literal );
  }
  // The maximum tokenized input must not retain all captures in memory.
  foreach ( [ 'python', 'go', 'rust', 'typescript', 'js' ] as $kind ) {
    $source = str_repeat ( ';', 65536 );
    $html = padHighlightTokens ( $source, $kind );
    if ( substr_count ( $html, 'class="hl-pun"' ) !== 65536 ) throw new Exception ( 'Tokenization boundary: ' . $kind );
  }
  $started = microtime ( TRUE );
  foreach ( [ 'python', 'go', 'rust', 'typescript' ] as $kind )
    foreach ( [ '"""' . str_repeat ( 'a\\"', 10000 ), '"' . str_repeat ( 'a\\"', 10000 ) . '\\', str_repeat ( '`\\', 10000 ) ] as $source ) {
      $html = padHighlightTokens ( $source, $kind );
      if ( html_entity_decode ( strip_tags ( $html ), ENT_QUOTES, 'UTF-8' ) !== $source ) throw new Exception ( 'Malformed source round trip: ' . $kind );
    }
  if ( microtime ( TRUE ) - $started > 3 ) throw new Exception ( 'Malformed tokenization work limit' );
  foreach ( [ 'python', 'go', 'rust', 'typescript', 'php', 'js' ] as $kind ) {
    $source = str_repeat ( '<"\\', 30000 );
    if ( padHighlightTokens ( $source, $kind ) !== padHighlightEscape ( $source ) ) throw new Exception ( 'Large input escaped fallback' );
  }
  echo "Highlighters: existing output, new grammars, aliases, escaping and long input passed\n";
?>
