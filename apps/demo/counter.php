<?php

  $title = 'Page Counter';
  $dataFile = DAT . 'demo/counter.json';

  if ( ! is_dir ( DAT . 'demo' ) )
    @mkdir ( DAT . 'demo', 0755, TRUE );

  $data = [ 'total' => 0, 'today' => 0, 'date' => date ( 'Y-m-d' ) ];
  if ( file_exists ( $dataFile ) ) {
    $json = file_get_contents ( $dataFile );
    $data = json_decode ( $json, TRUE ) ?: $data;
  }

  if ( $data ['date'] != date ( 'Y-m-d' ) ) {
    $data ['today'] = 0;
    $data ['date'] = date ( 'Y-m-d' );
  }

  $data ['total']++;
  $data ['today']++;

  file_put_contents ( $dataFile, json_encode ( $data, JSON_PRETTY_PRINT ) );

  $totalCount = $data ['total'];
  $todayCount = $data ['today'];
  $currentDate = date ( 'l, F j, Y' );

?>