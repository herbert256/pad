<?php

  // covariance.s - COVARIANCE.S(array1, array2) is the sample covariance of paired
  // numeric observations, divided by their count less one. Equal range dimensions are
  // required; nonnumeric pairs are omitted and fewer than two pairs gives #DIV/0!.
  //
  //   =COVARIANCE.S(B2:B3,B2:B3)   12.5
  //   =COVARIANCE.S(B2:B3,C2:C3)   1

include_once PAD.'subs/tables/formulas/_lib/paired.php';
return ['group'=>'Statistical','args'=>[2,2],'call'=>fn($args,$env)=>padFormulaPaired('covariance.s',$args)];
?>
