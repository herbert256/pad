<?php

  // sort - SORT(array, [sort_index], [sort_order], [by_col]) orders rows by a
  // one-based column (default 1), ascending (1) or descending (-1). TRUE for by_col
  // orders columns by a row instead. Equal keys keep input order; results spill.
  // Examples below show the first cell of the sorted column.
  //
  //   =SORT(B2:B7)        0
  //   =SORT(B2:B7,1,-1)   20

include_once PAD.'subs/formulas/_lib/dynamic.php';
return ['group'=>'Lookup','args'=>[1,4],'dynamic'=>TRUE,'call'=>fn($args,$env)=>padFormulaDynamic('sort',$args,$env)];
?>
