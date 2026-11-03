<?php
  include_once PAD . 'subs/tables/_lib/reshape.php';
  $rows = [ [ 'r' => 'A', 'c' => 'Q,1', 'v' => 4 ], [ 'r' => 'A', 'c' => 'Q,1', 'v' => 8 ], [ 'r' => 'B', 'c' => '<Q2>', 'v' => 3 ] ];
  $checks = [];
  foreach ( [ 'sum' => 12, 'count' => 2, 'avg' => 6, 'min' => 4, 'max' => 8 ] as $aggregate => $expected ) {
    [ $result, $fields, $labels ] = padTablePivot ( $rows, 'r', 'c', 'v', $aggregate );
    $checks [] = $result [0] ['value_0'] == $expected && $labels === [ 'R', 'Q,1', '<Q2>' ]
      && $result [0] ['value_1'] === ( in_array ( $aggregate, [ 'sum', 'count' ] ) ? 0 : NULL );
  }
  $html = padUnprotect ( padUnescape ( padCode ( "{table:pivot row='r', column='c', value='v'}" . json_encode ( $rows ) . '{/table:pivot}' ) ) );
  $answer = ! in_array ( FALSE, $checks, TRUE ) && str_contains ( $html, '&lt;Q2&gt;' ) && str_contains ( $html, '>Q,1</th>' ) ? 'five aggregates, missing cells and escaped exact labels' : 'FAIL';
?>
