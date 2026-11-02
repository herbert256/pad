<?php
  define ( 'PAD', dirname ( __DIR__, 3 ) . '/pad/' );
  function padDirList ( $p ) { return scandir ( $p ); }
  function padUnprotectQuotes ( $s ) { return $s; }
  function padWidgetOnce ( $s ) { return FALSE; }
  require PAD . 'lib/subsystem.php'; require PAD . 'lib/diff.php';
  function check ( $ok ) { if ( ! $ok ) throw new Exception ( 'Diff fixture failed' ); }
  check ( padDiffCompare ( 'json', '{"a":1,"b":null}', '{"b":null,"a":1}' ) === [] );
  check ( padDiffCompare ( 'json', '{}', '{"x":null}' ) === [ [ 'op' => 'add', 'path' => '/x', 'after' => NULL ] ] );
  check ( padDiffCompare ( 'json', '1', '1.0' ) [0] ['op'] === 'replace' );
  check ( count ( padDiffCompare ( 'json', '[1,2]', '[2,1]' ) ) === 2 );
  check ( padDiffCompare ( 'json', '{"a/b~":0}', '{"a/b~":false}' ) [0] ['path'] === '/a~1b~0' );
  $a = [ [ 'id' => 1, 'value' => 'x' ], [ 'id' => '1', 'value' => NULL ] ];
  check ( padDiffCompare ( 'rows', $a, array_reverse ( $a ), [ 'key' => 'id' ] ) === [] );
  foreach ( [ [ $a, [ $a [0], $a [0] ] ], [ [ [ 'value' => 1 ] ], [] ] ] as [ $old, $new ] ) {
    try { padDiffCompare ( 'rows', $old, $new, [ 'key' => 'id' ] ); throw new Exception ( 'Expected rejection' ); } catch ( InvalidArgumentException $e ) {}
  }
  check ( padDiffCompare ( 'words', 'old word', 'new word' ) [0] ['before'] === 'old' );
  $html = padDiffRender ( padDiffCompare ( 'json', '{}', '{"<script>":"<script>"}' ), TRUE, '<x>', '<y>' );
  check ( ! str_contains ( $html, '<script>' ) and str_contains ( $html, '&lt;script&gt;' ) and str_contains ( $html, '<ins>' ) );
  $big = array_fill ( 0, 5000, [ 'x' => 1 ] ); $new = $big; $new [4999] ['x'] = 2;
  check ( count ( padDiffCompare ( 'json', $big, $new ) ) === 1 );
  try { padDiffCompare ( 'json', array_fill ( 0, 20001, 1 ), [] ); throw new Exception ( 'Expected node limit' ); } catch ( LengthException $e ) {}
  echo "Diffs: strict types, missing/null, key ordering, collisions, escaping and bounded large input passed\n";
?>
