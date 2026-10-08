<?php

  // The chart the request asks for: its kind, how many months, which channels. The browser
  // gets the whole page; the component asks for &padFragment=chart with its choices and gets
  // the {fragment 'chart'} of chart.pad alone - the SVG PAD drew, nothing around it.

  $kind     = padRequest ( 'kind', 'bar' );
  $kind     = in_array ( $kind, [ 'bar', 'stacked', 'line', 'hbar' ] ) ? $kind : 'bar';
  $months   = max ( 3, min ( 12, (int) padRequest ( 'months', 12 ) ) );
  $channels = array_values ( array_intersect ( [ 'online', 'shop', 'wholesale' ], explode ( ',', (string) padRequest ( 'channels', 'online,shop,wholesale' ) ) ) );

  if ( ! $channels )
    $channels = [ 'online' ];

  $sales     = array_slice ( reactData ( 'sales' ), -$months );
  $value     = implode ( ', ', $channels );
  $stacked   = ( $kind == 'stacked' );
  $chartKind = $stacked ? 'bar' : $kind;
  $total     = array_sum ( array_map ( fn ( $row ) => array_sum ( array_intersect_key ( $row, array_flip ( $channels ) ) ), $sales ) );

  $choice = [ 'kind' => $kind, 'months' => $months, 'channels' => $channels ];

?>
