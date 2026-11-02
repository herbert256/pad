<?php
  // take - TAKE(array,rows,[columns]) selects up to the requested count. Negative counts operate from the end; oversized counts clamp to the dimension. Empty output returns #CALC!. DROP zero leaves that dimension unchanged; TAKE zero is empty.
  //   =TAKE(B2:B3,1)  10
include_once PAD.'subs/formulas/_lib/array-kit.php';
return ['group'=>'Lookup','args'=>[2,3],'dynamic'=>TRUE,'call'=>function($args,$env){$a=padFormulaMatrix($args[0]);if(padFormulaIsError($a))return $a;$nr=padFormulaArrayInteger($args[1],$env);$nc=isset($args[2])?padFormulaArrayInteger($args[2],$env):NULL;if($e=padFormulaFirstError($nr,$nc))return $e;if($nr===0||$nc===0)return padFormulaError('#CALC!');$rows=array_slice($a['range'],$nr<0?-min(-$nr,$a['h']):0,min(abs($nr),$a['h']));if($nc!==NULL)$rows=array_map(fn($r)=>array_slice($r,$nc<0?-min(-$nc,$a['w']):0,min(abs($nc),$a['w'])),$rows);return padFormulaArrayResult($rows);}];
