<?php
  // Load both public APIs in either order: php diffs-merge.php [tags|records].
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  function padDirList ( $p ) { return scandir ( $p ); }
  function padUnprotectQuotes ( $s ) { return $s; }
  function padUnprotect ( $s ) { return $s; }
  function padWidgetOnce ( $s ) { return FALSE; }
  function padMakeSafe ( $s, $n ) { return substr ( (string) $s, 0, $n ); }
  function padError ( $message ) { throw new InvalidArgumentException ( $message ); }
  function check ( $ok ) { if ( ! $ok ) throw new Exception ( 'Merged diff contract fixture failed' ); }
  require PAD . 'lib/subsystem.php';
  require PAD . 'lib/diff.php';
  if ( ( $argv [1] ?? 'tags' ) === 'records' ) {
    check ( padDiffCompare ( 'json', '{}', '{}' ) === [] );
    require PAD . 'subs/diffs/_lib/structured.php';
  } else {
    require PAD . 'subs/diffs/_lib/structured.php';
    check ( padDiffCompare ( 'json', '{}', '{}' ) === [] );
  }
  include_once PAD . 'subs/diffs/_lib/diff.php';
  include_once PAD . 'subs/diffs/_lib/text.php';
  set_error_handler ( function ( $n, $s ) { throw new Exception ( $s ); } );

  check ( padKinds ( 'diffs' ) === [ 'binary', 'json', 'lines', 'rows', 'sequence', 'side', 'unified', 'words' ] );
  $types = [];
  foreach ( padKinds ( 'diffs' ) as $kind ) $types [$kind] = include PAD . "subs/diffs/types/$kind.php";
  $options = [ 'from' => '<old>', 'to' => '<new>', 'same' => FALSE, 'key' => 'id', 'lines' => FALSE, 'side' => FALSE ];
  $old = "old <value>\n"; $new = "new <value>\n";
  check ( $types ['words'] ['render'] ( $old, $new, $options ) === padDiffWords ( $old, $new ) );
  check ( $types ['lines'] ['render'] ( $old, $new, $options ) === padDiffLines ( $old, $new, FALSE, '<old>', '<new>' ) );
  check ( $types ['side'] ['render'] ( $old, $new, $options ) === padDiffLines ( $old, $new, TRUE, '<old>', '<new>' ) );
  check ( $types ['words'] ['render'] ( $old, $new, array_replace ( $options, [ 'side' => TRUE ] ) ) === $types ['side'] ['render'] ( $old, $new, $options ) );
  check ( $types ['words'] ['render'] ( $old, $new, array_replace ( $options, [ 'lines' => TRUE ] ) ) === $types ['lines'] ['render'] ( $old, $new, $options ) );
  try { padDiffCompare ( 'side', $old, $new ); throw new Exception ( 'side must remain a tag-only layout' ); } catch ( InvalidArgumentException $e ) {}

  $tagRecords = padDiffJsonChanges ( padDiffValue ( '{}' ), padDiffValue ( '{"x":null}' ) );
  check ( $tagRecords === [ [ 'kind' => 'added', 'path' => '/x', 'before' => NULL, 'after' => NULL, 'had' => FALSE, 'has' => TRUE ] ] );
  $records = padDiffCompare ( 'json', '{}', '{"x":null}' );
  check ( $records === [ [ 'op' => 'add', 'path' => '/x', 'after' => NULL ] ] );
  check ( str_contains ( padDiffStructuredHtml ( $tagRecords, 'Before', 'After', 'json' ), 'pad-diff-absent">Absent' ) );
  check ( str_contains ( padDiffRender ( $records, TRUE ), '<ins>null</ins>' ) );
  check ( padDiffJsonChanges ( padDiffValue ( '{}' ), padDiffValue ( '[]' ) ) [0] ['kind'] === 'changed' );
  check ( padDiffJsonChanges ( padDiffValue ( '{}' ), padDiffValue ( '{}' ), TRUE ) [0] ['kind'] === 'same' );
  check ( padDiffJsonChanges ( padDiffValue ( '{"1":0,"01":1}' ), padDiffValue ( '{"1":0,"01":2}' ) ) [0] ['path'] === '/01' );
  $html = $types ['json'] ['render'] ( '{"<key>":null}', '{"<key>":"<script>","added":null}', $options );
  check ( str_contains ( $html, '&lt;old&gt;' ) && str_contains ( $html, '/&lt;key&gt;' ) && str_contains ( $html, '&lt;script&gt;' ) && ! str_contains ( $html, '<script>' ) );
  check ( str_contains ( $types ['json'] ['render'] ( '{}', '{}', array_replace ( $options, [ 'same' => TRUE ] ) ), 'pad-diff-same' ) );

  // Named tags match integer/string IDs; operation records preserve their distinct identities.
  $before = [ [ 'id' => 1, 'v' => 'x' ] ]; $after = [ [ 'id' => '1', 'v' => 'x' ] ];
  $tagRecords = padDiffRowChanges ( padDiffValue ( $before ), padDiffValue ( $after ), 'id' );
  check ( count ( $tagRecords ) === 1 && $tagRecords [0] ['path'] === '/1/id' && $tagRecords [0] ['kind'] === 'changed' );
  $records = padDiffCompare ( 'rows', $before, $after, [ 'key' => 'id' ] );
  check ( array_column ( $records, 'path' ) === [ '/i:1', '/s:1' ] && array_column ( $records, 'op' ) === [ 'remove', 'add' ] );
  try { padDiffRowChanges ( padDiffValue ( [ $before [0], $after [0] ] ), [], 'id' ); throw new Exception ( 'Named tags must reject normalized duplicate IDs' ); } catch ( InvalidArgumentException $e ) {}
  check ( str_contains ( $types ['rows'] ['render'] ( '[{"id":1,"v":"old"}]', '[{"id":1,"v":"new"}]', $options ), '/1/v' ) );
  check ( padDiffCompare ( 'rows', [ $before [0], $after [0] ], [ $after [0], $before [0] ], [ 'key' => 'id' ] ) === [] );

  foreach ( [ str_repeat ( ' ', 1048577 ) . '{}', str_repeat ( '[', 65 ) . '0' . str_repeat ( ']', 65 ), "\xFF" ] as $bad ) {
    try { padDiffCompare ( 'json', $bad, '{}' ); throw new Exception ( 'Comparison input bounds lost' ); } catch ( LengthException | JsonException | InvalidArgumentException $e ) {}
  }
  $cycle = new stdClass; $cycle->self = $cycle;
  try { padDiffCompare ( 'json', $cycle, new stdClass ); throw new Exception ( 'Native recursion must be bounded' ); } catch ( LengthException $e ) {}
  check ( ! is_file ( PAD . 'tags/diff.php' ) );
  echo 'Merged diffs: both contracts, eight tag layouts, helper limits and ' . ( $argv [1] ?? 'tags' ) . "-first load order passed\n";
?>
