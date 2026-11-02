<?php

  // rsq - RSQ(known_y, known_x) is squared Pearson correlation, from 0 to 1.
  // Equal range dimensions are required and nonnumeric pairs are omitted. Fewer than
  // two pairs, constant x or constant y gives #DIV/0!; cell errors propagate.
  //
  //   =RSQ(B2:B4,B2:B4)   1
  //   =RSQ(C2:C3,B2:B3)   1

include_once PAD.'subs/formulas/_lib/paired.php';
return ['group'=>'Statistical','args'=>[2,2],'call'=>fn($args,$env)=>padFormulaPaired('rsq',$args)];
?>
