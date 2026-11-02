<?php

  // unique - UNIQUE(array, [by_col], [exactly_once]) keeps distinct rows in input
  // order, comparing text without case. TRUE for by_col compares columns; TRUE for
  // exactly_once keeps only rows or columns seen once. The dense result spills.
  // Examples below show the first result cell; remaining values spill below it.
  //
  //   =UNIQUE(A2:A7)              Apples
  //   =UNIQUE(A2:A7,FALSE,TRUE)   Pears

include_once PAD.'subs/tables/formulas/_lib/dynamic.php';
return ['group'=>'Lookup','args'=>[1,3],'dynamic'=>TRUE,'call'=>fn($args,$env)=>padFormulaDynamic('unique',$args,$env)];
?>
