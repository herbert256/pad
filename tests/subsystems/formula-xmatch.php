<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  function padDirList ( $p ) { return scandir ( $p ); }
  require PAD . 'lib/subsystem.php';
  foreach ( [ 'formula', 'collect', 'functions', 'dates', 'format' ] as $lib ) require PAD . "subs/formulas/_lib/$lib.php";
  function runFormula ( $name, ...$args ) { return padFormulaType ( $name ) ['call'] ( $args, [] ); }
  function vector ( $values ) { return [ 'range'=>array_map(fn($v)=>[$v],$values), 'w'=>1,'h'=>count($values),'c'=>1,'r'=>1 ]; }
  function check ( $v ) { if ( ! $v ) throw new Exception ( 'formula fixture failed' ); }
  $v = vector ( [ 1.0, 3.0, 3.0, 6.0 ] );
  check ( runFormula ( 'xmatch', 3.0, $v ) === 2.0 );
  check ( runFormula ( 'xmatch', 3.0, $v, 0, -1 ) === 3.0 );
  check ( runFormula ( 'xmatch', 4.0, $v, -1 ) === 2.0 );
  check ( runFormula ( 'xmatch', 4.0, $v, 1, 2 ) === 4.0 );
  check ( runFormula ( 'xmatch', 4.0, vector([6.0,3.0,1.0]), -1, -2 ) === 2.0 );
  check ( runFormula ( 'xmatch', 'gr*', vector(['Apple','Grape']), 2 ) === 2.0 );
  check ( runFormula ( 'xmatch', 'a~*', vector(['abc','a*']), 2 ) === 2.0 );
  // Wildcard mode consumes the entire cell; a final newline is part of the value.
  check ( runFormula ( 'xmatch', 'a~*', vector(["a*\n",'a*']), 2 ) === 2.0 );
  check ( runFormula ( 'xmatch', 'a?', vector(["ab\n",'ab']), 2 ) === 2.0 );
  check ( runFormula ( 'xmatch', 'a*', vector(["a\n"]), 2 ) === 1.0 );
  check ( runFormula ( 'xmatch', 0, $v ) === padFormulaError ( '#N/A' ) );
  echo "formula:xmatch fixtures passed\n";
?>
