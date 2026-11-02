<?php

$ok = TRUE;
  foreach ( [ 'map'=>'maps', 'holidays'=>'holidays', 'search'=>'search', 'ai'=>'ai', 'format'=>'formats' ] as $prefix=>$directory )
    $ok = $ok && padSubsystemHas ( $prefix, 'auto' ) && in_array ( 'auto', padSubsystemTagKinds ( $prefix ), TRUE ) && ! padKind ( $directory, 'auto' );
  foreach ( [ 'map', 'holidays', 'search', 'ai', 'data', 'spreadsheet' ] as $tag )
    $ok = $ok && ! padTagBuiltIn ( $tag );
  $answer = $ok ? 'native selectors; real registries; original PAD tags removed' : 'FAIL';

?>
