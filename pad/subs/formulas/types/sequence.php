<?php
  // sequence - SEQUENCE(rows,[columns=1],[start=1],[step=1]) fills row-major. Dimensions must be integers; nonpositive dimensions return #CALC!, more than 10000 cells returns #NUM!, arithmetic overflow returns #NUM!. Negative and zero steps are allowed.
  //   =SEQUENCE(2,3,10,-2)  10
include_once PAD.'subs/formulas/_lib/array-kit.php';
return ['group'=>'Lookup','args'=>[1,4],'dynamic'=>TRUE,'call'=>function($args,$env){$h=padFormulaArrayInteger($args[0],$env);$w=padFormulaArrayInteger($args[1]??1,$env);$start=padFormulaArgNumber($args[2]??1,$env);$step=padFormulaArgNumber($args[3]??1,$env);if($e=padFormulaFirstError($h,$w,$start,$step))return $e;if($h<=0||$w<=0)return padFormulaError('#CALC!');if($h*$w>10000)return padFormulaError('#NUM!');$rows=[];for($r=0;$r<$h;$r++){ $row=[];for($c=0;$c<$w;$c++){ $v=$start+($r*$w+$c)*$step;if(!is_finite((float)$v))return padFormulaError('#NUM!');$row[]=$v;}$rows[]=$row;}return padFormulaArrayResult($rows);}];
