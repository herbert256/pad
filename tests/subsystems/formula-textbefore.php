<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  function padDirList ( $p ) { return scandir ( $p ); }
  require PAD . 'lib/subsystem.php';
  foreach ( [ 'formula', 'collect', 'functions', 'dates', 'format' ] as $lib ) require PAD . "subs/formulas/_lib/$lib.php";
  function runFormula ( $name, ...$args ) { return padFormulaType ( $name ) ['call'] ( $args, [] ); }
  function vector ( $values ) { return [ 'range'=>array_map(fn($v)=>[$v],$values), 'w'=>1,'h'=>count($values),'c'=>1,'r'=>1 ]; }
  function check ( $v ) { if ( ! $v ) throw new Exception ( 'formula fixture failed' ); }
  $name = 'textbefore';
  check ( runFormula($name,"Little red Riding Hood's red hood",'red',2) === "Little red Riding Hood's " );
  check ( runFormula($name,'A-b-c','-',-1) === 'A-b' );
  check ( runFormula($name,'AbCd','bc',1,1) === 'A' );
  check ( runFormula($name,'Socrates',' ',1,0,1) === 'Socrates' );
  check ( runFormula($name,'abc','',-1) === 'abc' );
  check ( runFormula($name,'ababa','aba',-1) === 'ab' );
  check ( runFormula($name,'abc','x',1,0,0,'none') === 'none' );
  check ( runFormula($name,'abc','x',0) === padFormulaError('#VALUE!') );
  check ( runFormula($name,'abc','x',4) === padFormulaError('#VALUE!') );
  check ( runFormula($name,'abc','x') === padFormulaError('#N/A') );
  check ( runFormula($name,str_repeat('x',32768),'x') === padFormulaError('#VALUE!') );
  echo "formula:textbefore fixtures passed\n";
?>
