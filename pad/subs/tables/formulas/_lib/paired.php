<?php
include_once PAD.'subs/statistics/_lib/statistics.php';
// Paired numeric observations: positions (including blanks/text) are aligned before filtering.
function padFormulaPairedMoments($args){
 $sets=[];$sizes=[];
 foreach($args as $arg){if(padFormulaIsError($arg))return $arg;$values=[];
  if(padFormulaIsRange($arg)){$sizes[]=[$arg['w'],$arg['h']];foreach($arg['range'] as $r=>$row)foreach($row as $c=>$v){if(padFormulaIsError($v))return $v;if(is_int($v)||is_float($v))$values[$r*$arg['w']+$c]=(float)$v;}}
  else{$sizes[]=[1,1];if(is_int($arg)||is_float($arg))$values[0]=(float)$arg;}$sets[]=$values;
 }
 if($sizes[0]!==$sizes[1])return padFormulaError('#N/A');
 $y=array_intersect_key($sets[0],$sets[1]);$x=array_intersect_key($sets[1],$sets[0]);$n=count($x);if(!$n)return padFormulaError('#DIV/0!');
 try{
  [$mx,$x]=padStatsCenter(array_values($x));[$my,$y]=padStatsCenter(array_values($y));
  $sx=max(array_map('abs',$x));$sy=max(array_map('abs',$y));if(!is_finite($sx)||!is_finite($sy))return padFormulaError('#NUM!');$sx=$sx?:1.0;$sy=$sy?:1.0;
  $x=array_map(fn($v)=>$v/$sx,$x);$y=array_map(fn($v)=>$v/$sy,$y);
  $xx=padStatsSum(array_map(fn($v)=>$v*$v,$x));$yy=padStatsSum(array_map(fn($v)=>$v*$v,$y));$xy=padStatsSum(array_map(fn($a,$b)=>$a*$b,$x,$y));
 }catch(InvalidArgumentException $e){return padFormulaError('#NUM!');}
 return compact('n','sx','sy','mx','my','xx','yy','xy');
}
function padFormulaPaired($kind,$args){
 $m=padFormulaPairedMoments($args);if(padFormulaIsError($m))return $m;extract($m);
 if(($kind==='covariance.s'&&$n<2)||(!str_starts_with($kind,'covariance.')&&($n<2||$xx<=0))||($kind==='rsq'&&$yy<=0))return padFormulaError('#DIV/0!');
 $slope=NULL;if($kind==='slope'||$kind==='intercept'){$ratio=$sy/$sx;$coefficient=$xy/$xx;$slope=is_finite($ratio)&&$ratio!=0?$coefficient*$ratio:$coefficient*$sy/$sx;}
 $result=match($kind){
  'slope'=>$slope,
  'intercept'=>$my-$slope*$mx,
  'rsq'=>max(0.0,min(1.0,($xy/sqrt($xx)/sqrt($yy))**2)),
  'covariance.p'=>($xy/$n*$sx)*$sy,
  'covariance.s'=>($xy/($n-1)*$sx)*$sy,
 };
 return is_finite($result)?$result:padFormulaError('#NUM!');
}
?>
