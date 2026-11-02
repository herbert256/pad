<?php

  // intercept - INTERCEPT(known_y, known_x) is the y intercept of the least-squares
  // line through paired numeric observations. Equal range dimensions are required;
  // nonnumeric pairs are omitted. Insufficient pairs or constant x gives #DIV/0!.
  //
  //   =INTERCEPT(B2:B4,B2:B4)   0
  //   =INTERCEPT(C2:C3,B2:B3)   0.4

include_once PAD.'subs/formulas/_lib/paired.php';
return ['group'=>'Statistical','args'=>[2,2],'call'=>fn($args,$env)=>padFormulaPaired('intercept',$args)];
?>
