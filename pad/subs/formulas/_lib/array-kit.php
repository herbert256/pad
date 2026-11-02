<?php
include_once PAD.'subs/formulas/_lib/dynamic.php';
function padFormulaArrayResult($rows){
 if(!$rows||!$rows[0])return padFormulaError('#CALC!');
 $h=count($rows);$w=count($rows[0]);if($h*$w>10000)return padFormulaError('#NUM!');
 return ['range'=>$rows,'h'=>$h,'w'=>$w,'c'=>1,'r'=>1,'dynamic'=>TRUE];
}
function padFormulaArrayInteger($value,$env){$n=padFormulaArgNumber($value,$env);if(padFormulaIsError($n))return $n;return is_finite((float)$n)&&$n==floor($n)&&abs($n)<=1000000?(int)$n:padFormulaError('#VALUE!');}
