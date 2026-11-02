<?php

  $selectedIcon = 'check';
  $selectedWidget = 'toast';
  $absentKind = 'missing';
  $checks = [];
  $checks ['optional dynamic kind'] = padCode ( '{icon:$absentKind optional/}' ) === '';
  $checks ['dynamic icon'] = str_contains ( padCode ( '{icon:$selectedIcon/}' ), 'pad-icon-check' );
  $checks ['dynamic pair'] = str_contains ( padCode ( "{widget:\$selectedWidget 'Saved'}Your changes{/widget:\$selectedWidget}" ), 'Your changes' );
  $checks ['automatic barcode'] = padCode ( "{barcode:auto '400638133393'/}" ) === padCode ( "{barcode:ean13 '400638133393'/}" );
  $checks ['automatic diagram'] = str_contains ( padCode ( "{diagram:auto}sequenceDiagram\nA ->> B: Hello{/diagram:auto}" ), 'Hello' );
  $checks ['auto node is not a header'] = padDiagramHeader ( "auto\nA --> B", FALSE ) === '';
  $checks ['automatic embed'] = str_contains ( padCode ( "{embed:auto 'https://youtu.be/aqz-KE-bpKQ', title='Video'/}" ), 'youtube-nocookie.com' );
  $expectedRows = padFakeRows ( 'customer:name, email', 2, 7 );
  $checks ['fake rows'] = padCode ( "{fake:rows 'customer:name, email', rows=2, seed=7}{\$customer}|{\$email};{/fake:rows}" )
                       === padCode ( '{array:expectedRows}{$customer}|{$email};{/array:expectedRows}' );
  $answer = implode ( '; ', array_map ( fn ( $name, $ok ) => $name . ': ' . ( $ok ? 'yes' : 'NO' ), array_keys ( $checks ), $checks ) );

?>
