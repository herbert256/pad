<?php
// Nonnegative contributions, sorted descending; ties retain input order.
function padChartPareto($rows,$width,$height) {
  [$numbers,$names]=padChartGuess($rows,TRUE); $used=[];
  $label=padChartPick('label',$names,$used); $field=padChartPick('value',$numbers,$used);
  if($field==='') return '';
  $points=[]; $omitted=0;
  foreach((array)$rows as $row){$row=padChartRow($row);$v=$row[$field]??NULL;
    if(!padChartFinite($v)||$v<0){$omitted++;continue;}
    $points[]=[padChartText($row,$label),$v+0,count($points)];}
  if(!$points)return '';
  usort($points,fn($a,$b)=>($b[1]<=>$a[1])?:($a[2]<=>$b[2]));
  $largest=max(array_column($points,1));$scaled=0;
  foreach($points as $p)$scaled+=$largest?$p[1]/$largest:0;
  $desc=['Negative and missing contributions omitted: '.$omitted];$sum=0;
  foreach($points as &$p){$sum+=$largest?$p[1]/$largest:0;$p[2]=$scaled?$sum/$scaled:0;$desc[]=$p[0].': '.padChartNumber($p[1]).', cumulative '.padChartNumber(round($p[2]*100,1)).'%';}unset($p);
  [, $svg]=padChartOpen('pareto',$points,$desc,(string)padTagParm('title','Pareto contributions'),$width,$height);
  $left=55;$right=$width-45;$top=24;$base=$height-46;$h=max(10,$base-$top);$step=max(1,($right-$left)/count($points));$path='';
  foreach([0,0.25,0.5,0.75,1] as $f){$y=padChartXY($base-$f*$h);$svg.='<line class="pc-grid" x1="'.$left.'" x2="'.$right.'" y1="'.$y.'" y2="'.$y.'"/><text x="'.($left-5).'" y="'.padChartXY($base-$f*$h+4).'" text-anchor="end">'.padChartNumber($largest*$f).'</text><text x="'.($right+5).'" y="'.padChartXY($base-$f*$h+4).'">'.($f*100).'%</text>';}
  foreach($points as $i=>$p){$x=$left+($i+0.5)*$step;$bar=$largest?$p[1]/$largest*$h:0;$tip=padChartAttr($desc[$i+1]);
    $svg.='<rect class="pc-c1" x="'.padChartXY($left+$i*$step+1).'" y="'.padChartXY($base-$bar).'" width="'.padChartXY(max(0.5,$step-2)).'" height="'.padChartXY($bar).'"><title>'.$tip.'</title></rect>';
    $path.=($i?'L':'M').padChartXY($x).','.padChartXY($base-$p[2]*$h);
    if($step>=30)$svg.='<text x="'.padChartXY($x).'" y="'.($base+16).'" text-anchor="middle">'.padChartAttr(padChartFit($p[0],$step-4)).'</text>';
  }
  return $svg.'<path class="pc-l2" d="'.$path.'" fill="none" stroke-width="2"/><text x="'.$left.'" y="'.($height-5).'">Contribution / cumulative percentage</text></svg>';
}
