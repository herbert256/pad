<?php

  // slope - SLOPE(known_y, known_x) is the least-squares slope of paired numeric
  // observations. Equal range dimensions are required; nonnumeric pairs are omitted.
  // Fewer than two numeric pairs or a constant x range gives #DIV/0!.
  //
  //   =SLOPE(B2:B4,B2:B4)   1
  //   =SLOPE(C2:C3,B2:B3)   0.08

include_once PAD.'subs/formulas/_lib/paired.php';
return ['group'=>'Statistical','args'=>[2,2],'call'=>fn($args,$env)=>padFormulaPaired('slope',$args)];
?>
