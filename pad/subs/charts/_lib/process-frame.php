<?php
function padChartProcessOption($name,$default){$v=padTagParm($name,$default);if(!is_string($v)||strlen($v)>200)throw new InvalidArgumentException('Chart '.$name.' must be text of at most 200 bytes');return $v;}
function padChartProcessRows($rows){if(count($rows)>2000)throw new InvalidArgumentException('Process charts support at most 2000 rows');return array_map('padChartRow',array_values($rows));}
function padChartProcessSeries($rows,$x,$value){$out=[];foreach(padChartProcessRows($rows) as $r){if(!padChartFinite($r[$x]??NULL))throw new InvalidArgumentException('Chart x must be finite');$v=$r[$value]??NULL;if($v!==NULL&&$v!==''&&!padChartFinite($v))throw new InvalidArgumentException('Chart value must be finite or missing');$out[]=[(float)$r[$x],$v===NULL||$v===''?NULL:(float)$v,$r];}usort($out,fn($a,$b)=>$a[0]<=>$b[0]);for($i=1;$i<count($out);$i++)if($out[$i][0]===$out[$i-1][0])throw new InvalidArgumentException('Duplicate chart x value');return $out;}
function padChartProcessFrame($kind,$points,$desc,$title,$w,$h,$xmin,$xmax,$ymin,$ymax){
 if($xmin===$xmax){$delta=max(1,abs($xmin)*.1);$xmin-=$delta;$xmax+=$delta;}if($ymin===$ymax){$delta=max(1,abs($ymin)*.1);$ymin-=$delta;$ymax+=$delta;}
 if(!is_finite($xmax-$xmin)||!is_finite($ymax-$ymin))throw new InvalidArgumentException('Chart numeric range is too large');
 $x=fn($v)=>50+($v-$xmin)/($xmax-$xmin)*($w-70);$y=fn($v)=>20+($ymax-$v)/($ymax-$ymin)*($h-60);
 [$id,$svg]=padChartOpen($kind,$points,$desc,$title,$w,$h);
 for($i=0;$i<=4;$i++){$v=$ymin+($ymax-$ymin)*($i/4);$yy=padChartXY($y($v));$xx=$xmin+($xmax-$xmin)*($i/4);$svg.='<path class="pc-grid" d="M50 '.$yy.'H'.($w-20).'"/><text x="44" y="'.padChartXY($y($v)+4).'" text-anchor="end">'.padChartNumber($v).'</text><text x="'.padChartXY($x($xx)).'" y="'.($h-15).'" text-anchor="middle">'.padChartNumber($xx).'</text>';}
 return [$svg,$x,$y];
}
function padChartProcessStepPath($points,$x,$y,$align,$gaps){$d='';$previous=NULL;foreach($points as $p){if($p[1]===NULL){if($gaps==='break')$previous=NULL;continue;}[$xx,$yy]=[$x($p[0]),$y($p[1])];if($previous===NULL)$d.='M'.padChartXY($xx).' '.padChartXY($yy);else{[$px,$py]=$previous;if($align==='before')$d.='V'.padChartXY($yy).'H'.padChartXY($xx);elseif($align==='after')$d.='H'.padChartXY($xx).'V'.padChartXY($yy);else{$mid=($px+$xx)/2;$d.='H'.padChartXY($mid).'V'.padChartXY($yy).'H'.padChartXY($xx);}}$previous=[$xx,$yy];}return $d;}
