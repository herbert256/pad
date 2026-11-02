<?php
// Shared SVG routing for explicit block grids and bounded C4 layouts.
function padDiagramBoxEdges($boxes,$edges,$id){
 $svg='<defs><marker id="'.$id.'-arrow" markerWidth="8" markerHeight="8" refX="7" refY="4" orient="auto"><path class="pd-head" d="M0 0L8 4L0 8Z"/></marker></defs>';
 foreach($edges as [$from,$to,$label]){$a=$boxes[$from];$b=$boxes[$to];$ax=$a['x']+$a['w']/2;$ay=$a['y']+$a['h']/2;$bx=$b['x']+$b['w']/2;$by=$b['y']+$b['h']/2;
  if($from===$to)$p=[[$a['x']+$a['w'],$ay-12],[$a['x']+$a['w']+30,$ay-12],[$a['x']+$a['w']+30,$ay+12],[$a['x']+$a['w'],$ay+12]];
  elseif(abs($bx-$ax)>=abs($by-$ay)){$dir=$bx>$ax?1:-1;$ax+=$dir*$a['w']/2;$bx-=$dir*$b['w']/2;$mid=($ax+$bx)/2;$p=[[$ax,$ay],[$mid,$ay],[$mid,$by],[$bx,$by]];}
  else{$dir=$by>$ay?1:-1;$ay+=$dir*$a['h']/2;$by-=$dir*$b['h']/2;$mid=($ay+$by)/2;$p=[[$ax,$ay],[$ax,$mid],[$bx,$mid],[$bx,$by]];}
  $path=padDiagramPath($p,'orthogonal',TRUE,'padChartXY','padChartXY');$svg.='<path class="pd-edge" marker-end="url(#'.$id.'-arrow)" d="'.$path.'"><title>'.padChartAttr($from.' to '.$to.': '.$label).'</title></path>';
  if($label!==''){[$x,$y]=padDiagramMiddle($p);$line=padChartFit($label,160);$w=padChartWidth($line)+8;$svg.='<rect class="pd-plate" x="'.padChartXY($x-$w/2).'" y="'.padChartXY($y-12).'" width="'.padChartXY($w).'" height="18"/>'.padDiagramText([$line],$x,$y-3);}}
 return $svg;
}
function padDiagramBoxNodes($boxes){
 $svg='';foreach($boxes as $box){$lines=padDiagramWrap($box['label'],max(8,(int)(($box['w']-18)/7)));$max=max(1,(int)(($box['h']-18)/16));if(count($lines)>$max){$lines=array_slice($lines,0,$max);$lines[$max-1]=padChartFit($lines[$max-1].'…',$box['w']-18);}$svg.='<rect class="pd-node" x="'.padChartXY($box['x']).'" y="'.padChartXY($box['y']).'" width="'.padChartXY($box['w']).'" height="'.padChartXY($box['h']).'" rx="5"><title>'.padChartAttr($box['label']).'</title></rect>'.padDiagramText($lines,$box['x']+$box['w']/2,$box['y']+$box['h']/2);}
 return $svg;
}
