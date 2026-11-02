<?php
  // hstack - HSTACK(array,...) combines 1..255 scalar or matrix arguments horizontally. Short dimensions are padded with #N/A cell errors. Input errors are preserved; output dimensions are checked before allocation.
  //   =HSTACK(1,2)  1
include_once PAD.'subs/formulas/_lib/array-kit.php';
return ['group'=>'Lookup','args'=>[1,255],'dynamic'=>TRUE,'call'=>function($args,$env){$mat=[];$h=0;$w=0;foreach($args as $arg){$a=padFormulaMatrix($arg);if(padFormulaIsError($a))return $a;$mat[]=$a;$w+=$a['w'];$h=max($h,$a['h']);if($h*$w>10000)return padFormulaError('#NUM!');}$rows=[];for($r=0;$r<$h;$r++){ $row=[];foreach($mat as $a)$row=array_merge($row,$a['range'][$r]??array_fill(0,$a['w'],padFormulaError('#N/A')));$rows[]=$row;}return padFormulaArrayResult($rows);}];
