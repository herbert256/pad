<?php
// Bounded static place/transition nets. No firing, timing or inhibitor-arc semantics.
function padDiagramPetriParse($text){
 if(strlen($text)>30000)return 'source exceeds 30000 bytes';$nodes=[];$edges=[];$seen=[];
 foreach(preg_split('/\R/',trim($text)) as $i=>$line){$line=trim($line);if($line===''||str_starts_with($line,'#'))continue;
  if(preg_match('/^(place|transition) ([A-Za-z][A-Za-z0-9_-]{0,31}) "([^"\r\n]{1,80})"(?: tokens=([0-9]{1,2}))?$/uD',$line,$m)){
   if(isset($nodes[$m[2]]))return 'duplicate identifier '.$m[2];if(count($nodes)>=60)return 'at most 60 nodes';if($m[1]==='transition'&&isset($m[4]))return 'only places have tokens';
   $nodes[$m[2]]=['kind'=>$m[1],'label'=>$m[3],'tokens'=>(int)($m[4]??0)];continue;
  }
  if(preg_match('/^([A-Za-z][A-Za-z0-9_-]{0,31}) -> ([A-Za-z][A-Za-z0-9_-]{0,31})(?: weight=([1-9][0-9]?))?$/D',$line,$m)){
   if(count($edges)>=200)return 'at most 200 arcs';$key=$m[1].':'.$m[2];if(isset($seen[$key]))return 'duplicate arc '.$key;$seen[$key]=TRUE;$edges[]=[$m[1],$m[2],(int)($m[3]??1)];continue;
  }return 'line '.($i+1).': expected place, transition, or an ordinary weighted arc';
 }
 if(!$nodes)return 'at least one node is required';
 foreach($edges as [$a,$b,$weight]){if(!isset($nodes[$a],$nodes[$b]))return 'arc names an unknown node';if($nodes[$a]['kind']===$nodes[$b]['kind'])return 'arcs must connect a place and a transition';}
 return ['nodes'=>$nodes,'edges'=>$edges];
}
function padDiagramPetriSvg($data,$title='Petri net'){
 $counts=['place'=>0,'transition'=>0];foreach($data['nodes'] as $node)$counts[$node['kind']]++;
 $width=640;$height=60+max($counts)*85;$pos=[];$next=['place'=>0,'transition'=>0];$desc=[];
 foreach($data['nodes'] as $id=>$node){$pos[$id]=[$node['kind']==='place'?170:470,45+85*$next[$node['kind']]++];$desc[]=$id.': '.$node['label'].($node['kind']==='place'?', '.$node['tokens'].' tokens':' transition');}
 foreach($data['edges'] as [$a,$b,$weight])$desc[]="$a to $b, weight $weight";
 [$id,$svg]=padChartOpen('diagram-petri',$data,$desc,$title,$width,$height);
 $svg.='<defs><marker id="'.$id.'-arrow" markerWidth="8" markerHeight="8" refX="7" refY="4" orient="auto"><path d="M0 0L8 4L0 8Z" fill="currentColor"/></marker></defs>';
 foreach($data['edges'] as [$a,$b,$weight]){[$x1,$y1]=$pos[$a];[$x2,$y2]=$pos[$b];$dx=$x2-$x1;$dy=$y2-$y1;$len=hypot($dx,$dy);$ra=$data['nodes'][$a]['kind']==='place'?25:min(16/max(1e-9,abs($dx)),28/max(1e-9,abs($dy)))*$len;$rb=$data['nodes'][$b]['kind']==='place'?28:min(16/max(1e-9,abs($dx)),28/max(1e-9,abs($dy)))*$len;$svg.='<path d="M'.padChartXY($x1+$dx/$len*$ra).' '.padChartXY($y1+$dy/$len*$ra).'L'.padChartXY($x2-$dx/$len*$rb).' '.padChartXY($y2-$dy/$len*$rb).'" fill="none" stroke="currentColor" marker-end="url(#'.$id.'-arrow)"/><text x="'.padChartXY(($x1+$x2)/2).'" y="'.padChartXY(($y1+$y2)/2-5).'">'.$weight.'</text>';}
 foreach($data['nodes'] as $key=>$node){[$x,$y]=$pos[$key];$svg.='<g><title>'.padChartAttr($key.': '.$node['label']).'</title>';
  $svg.=$node['kind']==='place'?'<circle cx="'.$x.'" cy="'.$y.'" r="25" fill="var(--pad-chart-surface, white)" stroke="currentColor"/>':'<rect x="'.($x-13).'" y="'.($y-25).'" width="26" height="50" fill="var(--pad-chart-surface, white)" stroke="currentColor"/>';
  if($node['kind']==='place'&&$node['tokens']>0){if($node['tokens']>5)$svg.='<text text-anchor="middle" x="'.$x.'" y="'.($y+4).'">'.$node['tokens'].'</text>';else for($n=0;$n<$node['tokens'];$n++){$a=$n*2*M_PI/$node['tokens'];$r=$node['tokens']===1?0:11;$svg.='<circle cx="'.padChartXY($x+round(cos($a),10)*$r).'" cy="'.padChartXY($y+round(sin($a),10)*$r).'" r="3" fill="currentColor"/>';}}
  $svg.='<text text-anchor="middle" x="'.$x.'" y="'.($y+42).'">'.padChartAttr(padChartFit($node['label'],260)).'</text></g>';
 }
 return $svg.'</svg>';
}
