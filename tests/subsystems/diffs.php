<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
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
  check ( padDiffCompare ( 'json', '{"1":0,"01":"a"}', '{"1":0,"01":"b"}' ) [0] ['path'] === '/01' );
  check ( count ( padDiffCompare ( 'json', '{"1":"a"}', '{"01":"b"}' ) ) === 2 );
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

  check ( padDiffCompare ( 'lines', "a\n", "a\n\n" ) === [ [ 'op' => 'add', 'path' => '/2', 'after' => '' ] ] );
  check ( padDiffCompare ( 'lines', "a", "a\n" ) === [ [ 'op' => 'add', 'path' => '/1', 'after' => '' ] ] );
  check ( padDiffCompare ( 'lines', "a\r\n", "a\n" ) === [] );
  check ( padDiffCompare ( 'lines', "", "\n" ) === [ [ 'op' => 'add', 'path' => '/0', 'after' => '' ], [ 'op' => 'add', 'path' => '/1', 'after' => '' ] ] );
  // Legacy HTML intentionally keeps its old newline normalization.
  check ( padDiffLines ( "a\n", "a\n\n", FALSE, '', '' ) === padDiffLines ( "a\n", "a\n", FALSE, '', '' ) );
  foreach ( [ (string) PHP_INT_MAX, (string) PHP_INT_MIN ] as $boundary ) check ( padDiffCompare ( 'json', $boundary, $boundary ) === [] );
  foreach ( [ '9223372036854775808', '-9223372036854775809', '123456789012345678901234567890', '{"v":9223372036854775808}' ] as $overflow ) {
    try { padDiffCompare ( 'json', $overflow, $overflow ); throw new Exception ( 'Expected integer range rejection' ); } catch ( InvalidArgumentException $e ) {}
  }
  $quoted = json_encode ( [ '9223372036854775808' => 'quoted "9223372036854775808" and \\123456789012345678901234567890' ] );
  check ( padDiffCompare ( 'json', $quoted, $quoted ) === [] );
  check ( padDiffCompare ( 'json', '1e2', '100.0' ) === [] );
  check ( padDiffCompare ( 'json', '{}', '[]' ) [0] ['op'] === 'replace' );
  echo "Diffs: strict types, missing/null, key ordering, collisions, escaping and bounded large input passed\n";
?>
