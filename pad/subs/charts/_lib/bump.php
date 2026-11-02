<?php
// Wide observations become competition ranks (1,1,3) separately in every period.
function padChartBump($rows,$width,$height){
 [$numbers,$names]=padChartGuess($rows,TRUE);$used=[];$label=padChartPick('label',$names,$used);$fields=padChartFields(padTagParm('value',''));if(!$fields)$fields=$numbers;if(count($fields)<2)return '';
 $points=[];foreach((array)$rows as $row){$row=padChartRow($row);$v=[];foreach($fields as $f)$v[]=padChartFinite($row[$f]??NULL)?$row[$f]+0:NULL;$points[]=[padChartText($row,$label),$v,[]];}if(!$points)return '';
 $maxRank=1;$desc=[];$any=FALSE;
 foreach($fields as $j=>$field){$values=[];foreach($points as $p)if($p[1][$j]!==NULL)$values[]=$p[1][$j];rsort($values,SORT_NUMERIC);foreach($points as &$p){$rank=$p[1][$j]===NULL?NULL:array_search($p[1][$j],$values)+1;$p[2][]=$rank;if($rank!==NULL){$maxRank=max($maxRank,$rank);$any=TRUE;}}unset($p);}
 if(!$any)return '';
 foreach($points as $p){$s=[];foreach($fields as $j=>$f)$s[]=$f.': '.($p[2][$j]===NULL?'missing':'rank '.$p[2][$j].' ('.padChartNumber($p[1][$j]).')');$desc[]=$p[0].': '.implode(', ',$s);}
 [, $svg]=padChartOpen('bump',$points,$desc,(string)padTagParm('title','Rank changes'),$width,$height);
 [$legend,$lh]=padChartLegend(array_column($points,0),12,6,$width-24);$svg.=$legend;$top=$lh+25;$bottom=max($top+10,$height-28);$left=35;$right=$width-25;$x=fn($j)=>$left+$j/max(1,count($fields)-1)*($right-$left);$y=fn($rank)=>$top+($rank-1)/max(1,$maxRank-1)*($bottom-$top);
 for($r=1;$r<=$maxRank;$r++){$yy=padChartXY($y($r));$svg.='<line class="pc-grid" x1="'.$left.'" x2="'.$right.'" y1="'.$yy.'" y2="'.$yy.'"/><text x="28" y="'.padChartXY($y($r)+4).'" text-anchor="end">'.$r.'</text>';}
 foreach($fields as $j=>$f)$svg.='<text x="'.padChartXY($x($j)).'" y="'.($height-8).'" text-anchor="middle">'.padChartAttr(padChartFit($f,max(15,($right-$left)/count($fields)))).'</text>';
 foreach($points as $i=>$p){$path='';$on=FALSE;$dots='';$c=$i%8+1;foreach($p[2] as $j=>$rank){if($rank===NULL){$on=FALSE;continue;}$xx=padChartXY($x($j));$yy=padChartXY($y($rank));$path.=($on?'L':'M').$xx.','.$yy;$on=TRUE;$dots.='<circle class="pc-c'.$c.'" cx="'.$xx.'" cy="'.$yy.'" r="3"><title>'.padChartAttr($p[0].', '.$fields[$j].': rank '.$rank.', value '.padChartNumber($p[1][$j])).'</title></circle>';}$svg.='<path class="pc-l'.$c.'" fill="none" stroke-width="2" d="'.$path.'"><title>'.padChartAttr($desc[$i]).'</title></path>'.$dots;}
 return $svg.'</svg>';
}
