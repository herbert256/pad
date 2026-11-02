<?php
// Dynamic ranges are dense matrices, capped independently of worksheet coordinate bounds.
function padFormulaMatrix($value){
 if(padFormulaIsError($value))return $value;
 if(!padFormulaIsRange($value))return ['range'=>[[$value]],'w'=>1,'h'=>1,'c'=>1,'r'=>1,'dynamic'=>TRUE];
 if($value['w']*$value['h']>10000)return padFormulaError('#NUM!');
 $rows=[];for($r=0;$r<$value['h'];$r++){ $row=[];for($c=0;$c<$value['w'];$c++)$row[]=padFormulaCell($value,$r,$c);$rows[]=$row; }
 return ['range'=>$rows,'w'=>$value['w'],'h'=>$value['h'],'c'=>1,'r'=>1,'dynamic'=>TRUE];
}
function padFormulaDynamic($name,$args,$env){
 $a=padFormulaMatrix($args[0]);if(padFormulaIsError($a))return $a;$rows=$a['range'];
 if($name==='sort')foreach($rows as $row)foreach($row as $v)if(padFormulaIsError($v))return $v;
 if($name==='filter'){
  $mask=padFormulaMatrix($args[1]);if(padFormulaIsError($mask))return $mask;
  $vertical=$mask['w']===1&&$mask['h']===$a['h'];$horizontal=$mask['h']===1&&$mask['w']===$a['w'];if(!$vertical&&!$horizontal)return padFormulaError('#VALUE!');
  $take=[];foreach(padFormulaFlat($mask) as $v){$v=padFormulaBool($v);if(padFormulaIsError($v))return $v;$take[]=$v;}
  $out=[];foreach($rows as $r=>$row){if($vertical){if($take[$r])$out[]=$row;}else{$selected=[];foreach($row as $c=>$v)if($take[$c])$selected[]=$v;if($selected)$out[]=$selected;}}
  if(!$out)return count($args)>2?(padFormulaIsRange($args[2])?padFormulaMatrix($args[2]):$args[2]):padFormulaError('#CALC!');$rows=$out;
 }elseif($name==='sort'){
  $index=padFormulaArgNumber($args[1]??1,$env);$order=padFormulaArgNumber($args[2]??1,$env);$bycol=padFormulaArgBool($args[3]??FALSE,$env);
  if($err=padFormulaFirstError($index,$order,$bycol))return $err;if($index!=floor($index)||$index<1||$index>($bycol?$a['h']:$a['w'])||!in_array($order,[-1,1],TRUE)&&!in_array($order,[-1.0,1.0],TRUE))return padFormulaError('#VALUE!');
  if($bycol)$rows=array_map(NULL,...$rows); // transpose; fix the single-row case below.
  if($bycol&&$a['h']===1)$rows=array_map(fn($v)=>[$v],$a['range'][0]);
  usort($rows,fn($x,$y)=>$order*padFormulaOrder($x[(int)$index-1],$y[(int)$index-1]));
  if($bycol){$trans=[];for($r=0;$r<$a['h'];$r++)$trans[]=array_column($rows,$r);$rows=$trans;}
 }else{
  $bycol=padFormulaArgBool($args[1]??FALSE,$env);$once=padFormulaArgBool($args[2]??FALSE,$env);if($err=padFormulaFirstError($bycol,$once))return $err;
  if($bycol){$trans=[];for($c=0;$c<$a['w'];$c++)$trans[]=array_column($rows,$c);$rows=$trans;}
  $keys=[];$counts=[];foreach($rows as $i=>$row){$normal=array_map(fn($v)=>is_string($v)?mb_strtolower($v,'UTF-8'):$v,$row);$key=serialize($normal);$keys[$i]=$key;$counts[$key]=($counts[$key]??0)+1;}
  $out=[];$seen=[];foreach($rows as $i=>$row){$key=$keys[$i];if(isset($seen[$key])||($once&&$counts[$key]!==1))continue;$seen[$key]=TRUE;$out[]=$row;}if(!$out)return padFormulaError('#CALC!');$rows=$out;
  if($bycol){$trans=[];for($r=0;$r<$a['h'];$r++)$trans[]=array_column($rows,$r);$rows=$trans;}
 }
 return ['range'=>$rows,'h'=>count($rows),'w'=>count($rows[0]),'c'=>1,'r'=>1,'dynamic'=>TRUE];
}
// Elementwise operators are enabled inside dynamic-array arguments, retaining scalar behavior elsewhere.
function padFormulaDynamicOperator($operator,$a,$b){
 if(!padFormulaIsRange($a)&&!padFormulaIsRange($b))return padFormulaOperator($operator,$a,$b);
 $a=padFormulaMatrix($a);$b=padFormulaMatrix($b);if($err=padFormulaFirstError($a,$b))return $err;
 $w=max($a['w'],$b['w']);$h=max($a['h'],$b['h']);if($w*$h>10000)return padFormulaError('#NUM!');
 if(($a['w']!==$w&&$a['w']!==1)||($b['w']!==$w&&$b['w']!==1)||($a['h']!==$h&&$a['h']!==1)||($b['h']!==$h&&$b['h']!==1))return padFormulaError('#VALUE!');
 $rows=[];for($r=0;$r<$h;$r++){ $row=[];for($c=0;$c<$w;$c++)$row[]=padFormulaOperator($operator,padFormulaCell($a,$a['h']===1?0:$r,$a['w']===1?0:$c),padFormulaCell($b,$b['h']===1?0:$r,$b['w']===1?0:$c));$rows[]=$row; }
 return ['range'=>$rows,'w'=>$w,'h'=>$h,'c'=>1,'r'=>1,'dynamic'=>TRUE];
}
?>
