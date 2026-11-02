<?php
// Ribbon intervals, lollipop values and three-part compositions. The geometry is
// mirrored in www/assets/charts/compositions.js; no statistical bounds are inferred.
function padChartCompositionData($kind,$rows,$options){
 if(!is_array($rows))return 'rows must be an array';if(count($rows)>1000)return 'at most 1000 rows';
 $out=[];$seen=[];
 foreach(array_values($rows) as $i=>$row){
  $row=padChartRow($row);$label=padChartText($row,$options['label']??'label');if($label==='')$label=(string)($i+1);
  if($kind==='lollipop'){
   $v=$row[$options['value']??'value']??NULL;if(!padChartFinite($v))return 'row '.($i+1).' needs a finite value';
   $out[]=[$label,$v+0];continue;
  }
  if($kind==='ternary'){
   $v=[];foreach(['a','b','c'] as $field){$n=$row[$options[$field]??$field]??NULL;if(!padChartFinite($n)||$n<0)return 'row '.($i+1).' needs three nonnegative finite components';$v[]=$n+0;}
   $sum=array_sum($v);if(!is_finite($sum)||$sum<=0)return 'row '.($i+1).' has a zero or invalid total';
   if(($options['normalize']??TRUE)===FALSE&&abs($sum-1)>1e-9)return 'row '.($i+1).' must total 1 when normalize=false';
   $out[]=[$label,$v[0]/$sum,$v[1]/$sum,$v[2]/$sum];continue;
  }
  $x=$row[$options['x']??'x']??NULL;if(!padChartFinite($x))return 'row '.($i+1).' needs a finite x';
  $key=$x==0?'0':sprintf('%.17g',(float)$x);if(isset($seen[$key]))return 'duplicate x at row '.($i+1);$seen[$key]=TRUE;
  $values=[];foreach(['value','low','high'] as $field){$v=$row[$options[$field]??$field]??NULL;if($v!==NULL&&!padChartFinite($v))return 'row '.($i+1).' needs finite interval values or null';$values[]=$v===NULL?NULL:$v+0;}
  if(!in_array(NULL,$values,TRUE)&&($values[1]>$values[2]||$values[0]<$values[1]||$values[0]>$values[2]))return 'row '.($i+1).' needs low <= value <= high';
  $out[]=[$label,$x+0,...$values];
 }
 if($kind==='ribbon'&&($options['order']??'ascending')==='ascending')usort($out,fn($a,$b)=>$a[1]<=>$b[1]);
 return $out;
}
function padChartComposition($kind,$rows,$width,$height){
 $options=[];foreach(['label',...($kind==='ternary'?['a','b','c']:($kind==='ribbon'?['x','value','low','high']:['value']))] as $key){$v=padTagParm($key,$key);if(!is_string($v)&&!is_int($v))return padChartCompositionError("$key must name a field");$options[$key]=(string)$v;}
 if($kind==='ribbon'){$options['order']=padTagParm('order','ascending');if(!in_array($options['order'],['ascending','input'],TRUE))return padChartCompositionError('order must be ascending or input');}
 if($kind==='ternary'){$options['normalize']=padTagParm('normalize',TRUE);if(!is_bool($options['normalize']))return padChartCompositionError('normalize must be true or false');}
 $points=padChartCompositionData($kind,$rows,$options);if(is_string($points))return padChartCompositionError($points);if(!$points)return '';
 $title=(string)padTagParm('title',ucfirst($kind));$desc=[];
 foreach($points as $p)$desc[]=$p[0].': '.implode(', ',array_map(fn($n)=>$n===NULL?'gap':padChartNumber($n),array_slice($p,1)));
 [, $svg]=padChartOpen($kind,$points,$desc,$title,$width,$height);
 $left=45;$right=$width-20;$top=24;$bottom=$height-35;$pw=max(1,$right-$left);$ph=max(1,$bottom-$top);
 $line=fn($x1,$y1,$x2,$y2,$class)=>'<line class="'.$class.'" x1="'.padChartXY($x1).'" y1="'.padChartXY($y1).'" x2="'.padChartXY($x2).'" y2="'.padChartXY($y2).'"/>';
 if($kind==='ternary'){
  $mid=($left+$right)/2;$svg.='<path class="pc-grid" d="M'.padChartXY($mid).' '.padChartXY($top).'L'.padChartXY($left).' '.padChartXY($bottom).'L'.padChartXY($right).' '.padChartXY($bottom).'Z" fill="none"/>';
  foreach([.25,.5,.75] as $v){$svg.=$line($mid*$v+$left*(1-$v),$bottom-$v*$ph,$mid*$v+$right*(1-$v),$bottom-$v*$ph,'pc-grid');}
  foreach([[$mid,$top-8,$options['a']],[$left,$bottom+20,$options['b']],[$right,$bottom+20,$options['c']]] as [$x,$y,$label])$svg.='<text text-anchor="middle" x="'.padChartXY($x).'" y="'.padChartXY($y).'">'.padChartAttr($label).'</text>';
  foreach($points as $i=>$p)$svg.='<circle class="pc-c1 pc-ring" cx="'.padChartXY($mid*$p[1]+$left*$p[2]+$right*$p[3]).'" cy="'.padChartXY($top*$p[1]+$bottom*($p[2]+$p[3])).'" r="5"><title>'.padChartAttr($desc[$i]).'</title></circle>';
 }else{
  $values=$kind==='lollipop'?[0]:[];foreach($points as $p)foreach(array_slice($p,$kind==='lollipop'?1:2) as $v)if($v!==NULL)$values[]=$v;if(!$values)return '';
  $ticks=padChartTicks(min($values),max($values),5);$lo=$ticks[0];$hi=end($ticks);if($hi===$lo)$hi=$lo+1;$y=fn($v)=>$bottom-($v-$lo)/($hi-$lo)*$ph;
  foreach($ticks as $t)$svg.=$line($left,$y($t),$right,$y($t),'pc-grid').'<text text-anchor="end" x="'.padChartXY($left-6).'" y="'.padChartXY($y($t)+4).'">'.padChartNumber($t).'</text>';
  if($kind==='lollipop'){
   $step=$pw/count($points);foreach($points as $i=>$p){$x=$left+($i+.5)*$step;$svg.='<g><title>'.padChartAttr($desc[$i]).'</title>'.$line($x,$y(0),$x,$y($p[1]),'pc-l1').'<circle class="pc-c1" cx="'.padChartXY($x).'" cy="'.padChartXY($y($p[1])).'" r="4"/></g>';
    if($i%max(1,(int)ceil(60/$step))===0)$svg.='<text text-anchor="middle" x="'.padChartXY($x).'" y="'.padChartXY($bottom+20).'">'.padChartAttr(padChartFit($p[0],max(20,$step-6))).'</text>';
   }
  }else{
   $xs=array_column($points,1);$xmin=min($xs);$xmax=max($xs);$x=fn($v)=>$left+($v-$xmin)/($xmax===$xmin?1:$xmax-$xmin)*$pw;
   $runs=[];$run=[];foreach($points as $p){if(in_array(NULL,array_slice($p,2),TRUE)){if($run)$runs[]=$run;$run=[];}else $run[]=$p;}if($run)$runs[]=$run;
   foreach($runs as $run){$poly=[];$estimate=[];foreach($run as $p){$poly[]=padChartXY($x($p[1])).','.padChartXY($y($p[3]));$estimate[]=padChartXY($x($p[1])).','.padChartXY($y($p[2]));}foreach(array_reverse($run) as $p)$poly[]=padChartXY($x($p[1])).','.padChartXY($y($p[4]));
    $svg.='<polygon class="pc-c1" opacity="0.2" points="'.implode(' ',$poly).'"/><polyline class="pc-l1" fill="none" points="'.implode(' ',$estimate).'"/>';
   }
   foreach($points as $i=>$p)if($p[2]!==NULL)$svg.='<circle class="pc-c1" cx="'.padChartXY($x($p[1])).'" cy="'.padChartXY($y($p[2])).'" r="3"><title>'.padChartAttr($desc[$i]).'</title></circle>';
   foreach(array_unique([$xmin,$xmax]) as $t)$svg.='<text text-anchor="middle" x="'.padChartXY($x($t)).'" y="'.padChartXY($bottom+20).'">'.padChartNumber($t).'</text>';
  }
 }
 return $svg.'</svg>';
}
function padChartCompositionError($text){if($GLOBALS['padCheckSyntax']??FALSE)padError('the chart: '.$text);return '';}
