<?php
// Fold each band of a signed series into one strip; threshold crossings are interpolated.
function padChartHorizon($rows,$width,$height){
 [$numbers,$names]=padChartGuess($rows,TRUE);$used=[];$label=padChartPick('label',$names,$used);$fields=padChartFields(padTagParm('value',''));if(!$fields)$fields=$numbers;if(!$fields)return '';
 $bands=padTagParm('bands',3);$bands=is_numeric($bands)?max(1,min(6,(int)$bands)):3;$points=[];$max=0;$desc=[];
 foreach((array)$rows as $row){$row=padChartRow($row);$v=[];foreach($fields as $f){$n=padChartFinite($row[$f]??NULL)?$row[$f]+0:NULL;$v[]=$n;if($n!==NULL)$max=max($max,abs($n));}$points[]=[padChartText($row,$label),$v];}
 if(!$points)return '';$band=padTagParm('band',0);$band=padChartFinite($band)&&$band>0?$band+0:($max?$max/$bands:1);$desc[]='Band width '.padChartNumber($band).'; '.$bands.' bands; positive color 1, negative color 2; values above range clipped';
 foreach($points as $p){$s=[];foreach($fields as $j=>$f)$s[]=$f.' '.($p[1][$j]===NULL?'missing':padChartNumber($p[1][$j]));$desc[]=$p[0].': '.implode(', ',$s);}
 $height=max($height,50+count($fields)*45);[, $svg]=padChartOpen('horizon',$points,$desc,(string)padTagParm('title','Horizon series'),$width,$height);$left=85;$right=$width-15;$top=30;$strip=($height-55)/count($fields);$x=fn($i)=>$left+$i/max(1,count($points)-1)*($right-$left);
 $svg.='<text x="8" y="15">'.padChartAttr('Each band: '.padChartNumber($band).'; color 1 + / color 2 −').'</text>';
 foreach($fields as $j=>$field){$base=$top+($j+1)*$strip-5;$h=max(5,$strip-10);$svg.='<text x="'.($left-6).'" y="'.padChartXY($base-$h/2).'" text-anchor="end">'.padChartAttr(padChartFit($field,$left-12)).'</text><line class="pc-grid" x1="'.$left.'" x2="'.$right.'" y1="'.padChartXY($base).'" y2="'.padChartXY($base).'"/>';
  foreach([1,-1] as $sign)for($b=0;$b<$bands;$b++){$runs=[];$run=[];foreach($points as $i=>$p){if($p[1][$j]===NULL){if($run)$runs[]=$run;$run=[];}else $run[]=[$x($i),$p[1][$j]*$sign];}if($run)$runs[]=$run;
   foreach($runs as $run){$poly=[];foreach($run as $k=>$p){if($k){$a=$run[$k-1];$cuts=[];foreach([$b*$band,($b+1)*$band] as $t)if(($a[1]<$t&&$p[1]>$t)||($a[1]>$t&&$p[1]<$t)){$f=($t-$a[1])/($p[1]-$a[1]);$cuts[]=[$f,$a[0]+$f*($p[0]-$a[0]),$t];}usort($cuts,fn($a,$b)=>$a[0]<=>$b[0]);foreach($cuts as $c)$poly[]=[$c[1],$c[2]];}$poly[]=$p;}
    $d='M'.padChartXY($run[0][0]).','.padChartXY($base);foreach($poly as $p)$d.='L'.padChartXY($p[0]).','.padChartXY($base-max(0,min(1,($p[1]-$b*$band)/$band))*$h);$d.='L'.padChartXY(end($run)[0]).','.padChartXY($base).'Z';$svg.='<path class="pc-c'.($sign===1?1:2).'" opacity="'.padChartXY(0.25+0.75*($b+1)/$bands).'" d="'.$d.'"/>';}}
 }
 foreach([0,count($points)-1] as $i)$svg.='<text x="'.padChartXY($x($i)).'" y="'.($height-5).'" text-anchor="'.($i?'end':'start').'">'.padChartAttr(padChartFit($points[$i][0],($right-$left)/2)).'</text>';
 return $svg.'</svg>';
}
