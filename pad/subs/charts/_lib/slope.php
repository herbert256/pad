<?php
// Two-period comparison with direct labels; label positions are separated from data points.
function padChartSlope($rows,$width,$height) {
  [$numbers,$names]=padChartGuess($rows,TRUE);$used=[];
  $label=padChartPick('label',$names,$used);$from=padChartPick('from',$numbers,$used);$to=padChartPick('to',$numbers,$used);
  if($from===''||$to===''||$from===$to)return '';
  $points=[];$values=[];$desc=[];
  foreach((array)$rows as $row){$row=padChartRow($row);$a=$row[$from]??NULL;$b=$row[$to]??NULL;$a=padChartFinite($a)?$a+0:NULL;$b=padChartFinite($b)?$b+0:NULL;if($a===NULL&&$b===NULL)continue;
    $name=padChartText($row,$label);$points[]=[$name,$a,$b];if($a!==NULL)$values[]=$a;if($b!==NULL)$values[]=$b;
    $desc[]=$name.': '.$from.' '.($a===NULL?'missing':padChartNumber($a)).', '.$to.' '.($b===NULL?'missing':padChartNumber($b));}
  if(!$points)return '';
  $height=max($height,60+count($points)*18);$low=min($values);$high=max($values);$range=$high-$low;$top=30;$base=$height-25;$left=min(150,$width/3);$right=$width-$left;
  $y=fn($v)=>$range?$base-($v-$low)/$range*($base-$top):($base+$top)/2;
  $labels=[[],[]];
  for($side=0;$side<2;$side++){$order=[];foreach($points as $i=>$p)if($p[$side+1]!==NULL)$order[]=[$i,$y($p[$side+1])];usort($order,fn($a,$b)=>($a[1]<=>$b[1])?:($a[0]<=>$b[0]));$last=$top-18;foreach($order as $p){$last=max($last+18,$p[1]);$labels[$side][$p[0]]=$last;}$last=$base+18;foreach(array_reverse($order) as $p){$last=min($last-18,$labels[$side][$p[0]]);$labels[$side][$p[0]]=$last;}}
  [, $svg]=padChartOpen('slope',$points,$desc,(string)padTagParm('title','Slope comparison'),$width,$height);
  foreach([$from,$to] as $s=>$name){$x=$s?$right:$left;$svg.='<text x="'.padChartXY($x).'" y="16" text-anchor="middle">'.padChartAttr(padChartFit($name,$width/2-8)).'</text><line class="pc-axis" x1="'.padChartXY($x).'" x2="'.padChartXY($x).'" y1="'.$top.'" y2="'.$base.'"/>';}
  foreach($points as $i=>$p){$c=$i%8+1;if($p[1]!==NULL&&$p[2]!==NULL)$svg.='<path class="pc-l'.$c.'" fill="none" stroke-width="2" d="M'.padChartXY($left).','.padChartXY($y($p[1])).'L'.padChartXY($right).','.padChartXY($y($p[2])).'"><title>'.padChartAttr($desc[$i]).'</title></path>';
    for($s=0;$s<2;$s++){if($p[$s+1]===NULL)continue;$x=$s?$right:$left;$v=$y($p[$s+1]);$ly=$labels[$s][$i];$tx=$x+($s?8:-8);$text=$p[0].' '.padChartNumber($p[$s+1]);$svg.='<circle class="pc-c'.$c.'" cx="'.padChartXY($x).'" cy="'.padChartXY($v).'" r="3"/><line class="pc-l'.$c.'" x1="'.padChartXY($x).'" y1="'.padChartXY($v).'" x2="'.padChartXY($tx).'" y2="'.padChartXY($ly).'"/><text x="'.padChartXY($tx).'" y="'.padChartXY($ly+4).'" text-anchor="'.($s?'start':'end').'">'.padChartAttr(padChartFit($text,$left-12)).'<title>'.padChartAttr($text).'</title></text>';}}
  return $svg.'</svg>';
}
