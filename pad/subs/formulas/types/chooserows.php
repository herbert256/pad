<?php
  // chooserows - CHOOSEROWS(array,index,...) uses one-based indices; negatives count from the end. Zero, noninteger and out-of-range indices return #VALUE!. Repeated indices duplicate the selected row.
  //   =CHOOSEROWS(A2:B3,2)  Pears
include_once PAD.'subs/formulas/_lib/array-kit.php';
return ['group'=>'Lookup','args'=>[2,255],'dynamic'=>TRUE,'call'=>function($args,$env){$a=padFormulaMatrix($args[0]);if(padFormulaIsError($a))return $a;$indices=[];$size=$a['h'];foreach(array_slice($args,1) as $arg){$i=padFormulaArrayInteger($arg,$env);if(padFormulaIsError($i))return $i;if(!$i||abs($i)>$size)return padFormulaError('#VALUE!');$indices[]=$i>0?$i-1:$size+$i;}$rows=[];if(count($indices)*$a['w']>10000)return padFormulaError('#NUM!');foreach($indices as $i)$rows[]=$a['range'][$i];return padFormulaArrayResult($rows);}];
