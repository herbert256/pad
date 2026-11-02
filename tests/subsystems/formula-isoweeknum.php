<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  function padDirList ( $p ) { return scandir ( $p ); }
  require PAD . 'lib/subsystem.php';
  foreach ( [ 'formula', 'collect', 'functions', 'dates', 'format' ] as $lib ) require PAD . "subs/tables/formulas/_lib/$lib.php";
  function runFormula ( $name, ...$args ) { return padFormulaType ( $name ) ['call'] ( $args, [] ); }
  function vector ( $values ) { return [ 'range'=>array_map(fn($v)=>[$v],$values), 'w'=>1,'h'=>count($values),'c'=>1,'r'=>1 ]; }
  function check ( $v ) { if ( ! $v ) throw new Exception ( 'formula fixture failed' ); }
  foreach ( [ [2012,3,9,10], [2021,1,1,53], [2021,1,4,1], [2018,12,31,1], [2020,12,31,53], [2026,1,1,1] ] as [$y,$m,$d,$want] )
    check ( runFormula ( 'isoweeknum', padFormulaSerial($y,$m,$d) ) === (float)$want );
  check ( runFormula('isoweeknum',-1) === padFormulaError('#NUM!') );
  // Independent Gregorian date oracle for every day of four years, including leap day.
  for ( $d = 43831; $d <= 45291; $d++ ) {
    [$y,$m,$day] = padFormulaYmd($d);
    $reference = (int)(new DateTimeImmutable(sprintf('%04d-%02d-%02d',$y,$m,$day)))->format('W');
    check ( runFormula('isoweeknum',$d) === (float)$reference );
  }
  echo "formula:isoweeknum fixtures passed\n";
?>
