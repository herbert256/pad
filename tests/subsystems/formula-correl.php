<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  function padDirList ( $p ) { return scandir ( $p ); }
  require PAD . 'lib/subsystem.php';
  foreach ( [ 'formula', 'collect', 'functions', 'dates', 'format' ] as $lib ) require PAD . "subs/tables/formulas/_lib/$lib.php";
  function runFormula ( $name, ...$args ) { return padFormulaType ( $name ) ['call'] ( $args, [] ); }
  function vector ( $values ) { return [ 'range'=>array_map(fn($v)=>[$v],$values), 'w'=>1,'h'=>count($values),'c'=>1,'r'=>1 ]; }
  function check ( $v ) { if ( ! $v ) throw new Exception ( 'formula fixture failed' ); }
  $x = vector([3,2,4,5,6]); $y = vector([9,7,12,15,17]);
  check ( abs(runFormula('correl',$x,$y)-0.997054486) < 1e-9 );
  check ( abs(runFormula('correl',vector([1,NULL,3,'4',5]),vector([2,9,6,8,10]))-1) < 1e-12 );
  check ( runFormula('correl',vector([1,1]),vector([2,3])) === padFormulaError('#DIV/0!') );
  check ( runFormula('correl',vector([1,2]),vector([1])) === padFormulaError('#N/A') );
  check ( runFormula('correl',vector([1,padFormulaError('#REF!')]),vector([1,2])) === padFormulaError('#REF!') );
  check ( abs(runFormula('correl',vector([1e300,2e300,3e300]),vector([-1e300,-2e300,-3e300]))+1) < 1e-12 );
  echo "formula:correl fixtures passed\n";
?>
