<?php
  // transpose - TRANSPOSE(array) swaps rows and columns. Scalars produce a 1×1 matrix. Cell errors retain their positions after transposition.
  //   =TRANSPOSE(B2:B3)  10
include_once PAD.'subs/formulas/_lib/array-kit.php';
return ['group'=>'Lookup','args'=>[1,1],'dynamic'=>TRUE,'call'=>function($args,$env){$a=padFormulaMatrix($args[0]);if(padFormulaIsError($a))return $a;$rows=[];for($c=0;$c<$a['w'];$c++)$rows[]=array_column($a['range'],$c);return padFormulaArrayResult($rows);}];
