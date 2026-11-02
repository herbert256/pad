<?php

  $kind = 'newmanConway';
  $values = '3;1;2';
  $actionKind = 'reverse';
  $playKind = 'prime';
  $makeKind = 'add';
  $source = '1..5';
  $emptyCount = 0;
  $missingKind = 'missingSequenceKind';
  $checks = [];
  $render = fn ( $source ) => trim ( padCode ( $source ) );

  $checks ['range'] = $render ( "{sequence:range '1..5'}{\$sequence}{/sequence:range}" ) === '12345';
  $checks ['dynamic kind'] = $render ( '{sequence:$kind rows=5}{$sequence},{/sequence:$kind}' )
                            === implode ( ',', pqArray ( 'newmanConway', '', 'rows=5' ) ) . ',';
  $checks ['runtime range'] = $render ( '{sequence:auto $source}{$sequence}{/sequence:auto}' ) === '12345';
  $checks ['runtime list'] = $render ( '{sequence:auto $values, sort}{$sequence}{/sequence:auto}' ) === '123';
  $checks ['zero count'] = $render ( '{sequence:auto $emptyCount}{$sequence}@else@empty{/sequence:auto}' ) === 'empty';
  $checks ['keep'] = $render ( "{keep:prime '1..10'}{\$sequence},{/keep:prime}" ) === '2,3,5,7,';
  $checks ['remove'] = $render ( "{remove:prime '1..10'}{\$sequence},{/remove:prime}" ) === '1,4,6,8,9,10,';
  $checks ['make'] = $render ( '{make:add 3, rows=4}{$sequence}{/make:add}' ) === '4567';
  $checks ['action'] = $render ( "{action:reverse '1..5'}{\$sequence}{/action:reverse}" ) === '54321';
  $checks ['ordered actions'] = $render ( "{sequence:auto '1..5', sort, 'reverse'}{\$sequence}{/sequence:auto}" ) === '54321';
  $checks ['stored values'] = $render ( "{sequence:range '1..4', push='qualifiedValues'/}{make:add 10, pull='qualifiedValues'}{\$sequence},{/make:add}" ) === '11,12,13,14,';
  $checks ['dynamic action'] = $render ( "{action:\$actionKind '1..5'}{\$sequence}{/action:\$actionKind}" ) === '54321';
  $checks ['dynamic play'] = $render ( "{keep:\$playKind '1..10'}{\$sequence},{/keep:\$playKind}" ) === '2,3,5,7,';
  $checks ['dynamic remove'] = $render ( "{remove:\$playKind '1..5'}{\$sequence},{/remove:\$playKind}" ) === '1,4,';
  $checks ['dynamic make'] = $render ( '{make:$makeKind 3, rows=4}{$sequence}{/make:$makeKind}' ) === '4567';
  $checks ['literal list'] = $render ( '{sequence:auto [3,1,2], sort}{$sequence}{/sequence:auto}' ) === '123';
  $checks ['optional missing kind'] = $render ( '{sequence:$missingKind optional/}' ) === '';

  $removed = 0;
  foreach ( padSequenceOperations as $operation )
    if ( ! is_file ( PAD . "tags/$operation.php" ) && ! is_file ( PQ . "start/tags/$operation.php" )
         && ! padTypeTagCheck ( 'pad', $operation ) )
      $removed++;
  $checks ['wrappers removed'] = $removed === 5;

  $answer = implode ( '; ', array_map ( fn ( $name, $ok ) => $name . ': ' . ( $ok ? 'yes' : 'NO' ), array_keys ( $checks ), $checks ) );

?>
