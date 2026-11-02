<?php
function padFaulttreeRead($text){
 if(strlen($text)>20000)throw new InvalidArgumentException('Fault tree source exceeds 20000 bytes');$nodes=[];$edges=[];
 foreach(preg_split('/\R/',trim($text)) as $i=>$line){$line=trim($line);if($line===''||str_starts_with($line,'#'))continue;
 if(preg_match('/^(top|event|and|or) ([A-Za-z][\w-]{0,31}) "([^"\r\n]{1,80})"$/uD',$line,$m)){if(isset($nodes[$m[2]])||count($nodes)>=40)throw new InvalidArgumentException('Duplicate node or more than 40 nodes');$nodes[$m[2]]=['kind'=>$m[1],'label'=>$m[3],'children'=>[]];continue;}
 if(preg_match('/^([A-Za-z][\w-]{0,31}) -> ([A-Za-z][\w-]{0,31})$/D',$line,$m)){if(count($edges)>=39)throw new InvalidArgumentException('At most 39 tree edges');$edges[]=[$m[1],$m[2]];continue;}throw new InvalidArgumentException('Unsupported fault tree syntax on line '.($i+1));}
 $parents=[];foreach($edges as [$a,$b]){if(!isset($nodes[$a],$nodes[$b])||isset($parents[$b])||$a===$b)throw new InvalidArgumentException('Edges require known nodes and one parent per node');$parents[$b]=$a;$nodes[$a]['children'][]=$b;}
 $roots=array_diff(array_keys($nodes),array_keys($parents));if(count($roots)!==1)throw new InvalidArgumentException('Fault tree needs one root');$root=reset($roots);if($nodes[$root]['kind']!=='top')throw new InvalidArgumentException('Root must be a top event');
 $visited=[];$walk=function($id)use(&$walk,&$visited,$nodes){if(isset($visited[$id]))throw new InvalidArgumentException('Fault tree cycle');$visited[$id]=TRUE;foreach($nodes[$id]['children'] as $child)$walk($child);};$walk($root);if(count($visited)!==count($nodes))throw new InvalidArgumentException('Disconnected cycle');
 foreach($nodes as $key=>$n){$count=count($n['children']);if(($n['kind']==='event'&&$count)||($n['kind']==='top'&&($key!==$root||$count!==1))||(in_array($n['kind'],['and','or'])&&$count<2))throw new InvalidArgumentException('Top needs one child; AND/OR need at least two; basic events are leaves');}
 return ['root'=>$root,'nodes'=>$nodes,'edges'=>$edges];
}
function padFaulttreeSvg($d,$title='Fault tree'){
 $pos=[];$leaf=0;$maxDepth=0;$walk=function($key,$depth)use(&$walk,&$pos,&$leaf,&$maxDepth,$d){$maxDepth=max($maxDepth,$depth);$xs=[];foreach($d['nodes'][$key]['children'] as $c){$walk($c,$depth+1);$xs[]=$pos[$c][0];}$pos[$key]=[$xs?array_sum($xs)/count($xs):80+160*$leaf++,55+110*$depth];};$walk($d['root'],0);$desc=[];
 foreach($d['nodes'] as $key=>$n)$desc[]=$key.': '.strtoupper($n['kind']).' '.$n['label'].($n['children']?', '.($n['kind']==='and'?'all inputs required: ':($n['kind']==='or'?'any input sufficient: ':'caused by ')).implode(', ',$n['children']):'');
 [$id,$svg]=padChartOpen('diagram-faulttree',$d,$desc,$title,max(320,$leaf*160),110*($maxDepth+1));
 foreach($d['edges'] as [$a,$b]){[$x,$y]=$pos[$a];[$xx,$yy]=$pos[$b];$mid=($y+$yy)/2;$svg.='<path d="M'.$x.' '.($y+25).'V'.$mid.'H'.$xx.'V'.($yy-25).'" fill="none" stroke="currentColor"/>';}
 foreach($d['nodes'] as $key=>$n){[$x,$y]=$pos[$key];$fill=' fill="var(--pad-chart-surface,white)" stroke="currentColor"';$svg.='<g><title>'.padChartAttr(strtoupper($n['kind']).': '.$n['label']).'</title>';
 if($n['kind']==='event')$svg.='<circle cx="'.$x.'" cy="'.$y.'" r="24"'.$fill.'/>';
 elseif($n['kind']==='and')$svg.='<path d="M'.($x-28).' '.($y+25).'V'.$y.'a28 28 0 0 1 56 0V'.($y+25).'Z"'.$fill.'/>';
 elseif($n['kind']==='or')$svg.='<path d="M'.($x-28).' '.($y+25).'Q'.($x-32).' '.($y-12).' '.$x.' '.($y-28).'Q'.($x+32).' '.($y-12).' '.($x+28).' '.($y+25).'Q'.$x.' '.($y+12).' '.($x-28).' '.($y+25).'Z"'.$fill.'/>';
 else $svg.='<rect x="'.($x-55).'" y="'.($y-24).'" width="110" height="48"'.$fill.'/>';
 $svg.='<text x="'.$x.'" y="'.($y+4).'" text-anchor="middle">'.strtoupper($n['kind']).'</text><text x="'.$x.'" y="'.($y+46).'" text-anchor="middle">'.padChartAttr(padChartFit($n['label'],150)).'</text></g>';}
 return $svg.'</svg>';
}
