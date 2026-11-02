<?php

  // covariance.p - COVARIANCE.P(array1, array2) is the population covariance of paired
  // numeric observations. Ranges must have equal dimensions; pairs containing text or
  // blanks are omitted together. A cell error propagates and no numeric pairs is #DIV/0!.
  //
  //   =COVARIANCE.P(B2:B3,B2:B3)   6.25
  //   =COVARIANCE.P(B2:B3,C2:C3)   0.5

include_once PAD.'subs/tables/formulas/_lib/paired.php';
return ['group'=>'Statistical','args'=>[2,2],'call'=>fn($args,$env)=>padFormulaPaired('covariance.p',$args)];
?>
