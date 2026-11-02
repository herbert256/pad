<?php
// vstack - VSTACK(array,...) combines 1..255 scalar or matrix arguments vertically. Short dimensions are padded with #N/A cell errors. Input errors are preserved; output dimensions are checked before allocation.
include_once PAD.'subs/formulas/_lib/array-kit.php';
return ['group'=>'Lookup','args'=>[1,255],'dynamic'=>TRUE,'call'=>function($args,$env){$mat=[];$h=0;$w=0;foreach($args as $arg){$a=padFormulaMatrix($arg);if(padFormulaIsError($a))return $a;$mat[]=$a;$h+=$a['h'];$w=max($w,$a['w']);if($h*$w>10000)return padFormulaError('#NUM!');}$rows=[];foreach($mat as $a)foreach($a['range'] as $row)$rows[]=array_pad($row,$w,padFormulaError('#N/A'));return padFormulaArrayResult($rows);}];
