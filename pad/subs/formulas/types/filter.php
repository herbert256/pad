<?php

  // filter - FILTER(array, include, [if_empty]) keeps rows or columns selected by a
  // matching one-column or one-row Boolean mask. Comparison expressions can produce the
  // mask. The dense result spills into empty cells; no matches gives if_empty or #CALC!.
  // Examples below show the first result cell; other selected rows spill below it.
  //
  //   =FILTER(A2:A7,B2:B7>10)           Cherries
  //   =FILTER(A2:A7,B2:B7>100,"none")   none

include_once PAD.'subs/formulas/_lib/dynamic.php';
return ['group'=>'Lookup','args'=>[2,3],'dynamic'=>TRUE,'call'=>fn($args,$env)=>padFormulaDynamic('filter',$args,$env)];
?>
