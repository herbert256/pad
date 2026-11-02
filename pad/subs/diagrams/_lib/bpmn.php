<?php
// Drawing-only BPMN subset; no XML interchange or execution semantics.
function padBpmnRead($text){
 if(strlen($text)>20000)throw new InvalidArgumentException('BPMN source exceeds 20000 bytes');$lanes=[];$nodes=[];$edges=[];
 foreach(preg_split('/\R/',trim($text)) as $i=>$line){$line=trim($line);if($line===''||str_starts_with($line,'#'))continue;
 if(preg_match('/^lane ([a-zA-Z][\w-]{0,31}) "([^"\r\n]{1,80})"$/uD',$line,$m)){if(isset($lanes[$m[1]])||count($lanes)>=8)throw new InvalidArgumentException('Duplicate lane or more than eight lanes');$lanes[$m[1]]=$m[2];continue;}
 if(preg_match('/^(start|end|task|exclusive|parallel) ([a-zA-Z][\w-]{0,31}) ([a-zA-Z][\w-]{0,31}) "([^"\r\n]{1,80})"$/uD',$line,$m)){if(isset($nodes[$m[2]])||count($nodes)>=40)throw new InvalidArgumentException('Duplicate node or more than 40 nodes');$nodes[$m[2]]=['kind'=>$m[1],'lane'=>$m[3],'label'=>$m[4]];continue;}
 if(preg_match('/^([a-zA-Z][\w-]{0,31}) -> ([a-zA-Z][\w-]{0,31})(?: "([^"\r\n]{1,80})")?$/uD',$line,$m)){if(count($edges)>=100)throw new InvalidArgumentException('At most 100 sequence flows');$edges[]=[$m[1],$m[2],$m[3]??''];continue;}
 throw new InvalidArgumentException('Unsupported BPMN drawing syntax on line '.($i+1));}
 if(!$lanes||!$nodes)throw new InvalidArgumentException('BPMN drawing needs lanes and nodes');foreach($nodes as $node)if(!isset($lanes[$node['lane']]))throw new InvalidArgumentException('Unknown lane');
 foreach($edges as [$a,$b])if(!isset($nodes[$a],$nodes[$b])||$a===$b||$nodes[$a]['kind']==='end'||$nodes[$b]['kind']==='start')throw new InvalidArgumentException('Invalid sequence flow');
 return ['lanes'=>$lanes,'nodes'=>$nodes,'edges'=>$edges];
}
function padBpmnSvg($d,$title='Business process'){
 $width=140+count($d['nodes'])*150;$height=count($d['lanes'])*140;$pos=[];$laneIndex=array_flip(array_keys($d['lanes']));$desc=[];$i=0;
 foreach($d['nodes'] as $key=>$n){$pos[$key]=[180+150*$i++,70+140*$laneIndex[$n['lane']]];$desc[]=$key.': '.$n['kind'].' '.$n['label'].', lane '.$d['lanes'][$n['lane']];}
 foreach($d['edges'] as [$a,$b,$label])$desc[]=$a.' to '.$b.($label!==''?', '.$label:'');[$id,$svg]=padChartOpen('diagram-bpmn',$d,$desc,$title,$width,$height);
 $svg.='<defs><marker id="'.$id.'-a" markerWidth="8" markerHeight="8" refX="8" refY="4" orient="auto"><path d="M0 0L8 4L0 8Z" fill="currentColor"/></marker></defs>';
 foreach($d['lanes'] as $key=>$label){$y=$laneIndex[$key]*140;$svg.='<rect x="1" y="'.($y+1).'" width="'.($width-2).'" height="138" fill="none" stroke="currentColor"/><text x="8" y="'.($y+22).'">'.padChartAttr(padChartFit($label,110)).'</text>';}
 foreach($d['edges'] as [$a,$b,$label]){[$x1,$y1]=$pos[$a];[$x2,$y2]=$pos[$b];$x1+=48;$x2-=48;$mid=($x1+$x2)/2;$svg.='<path d="M'.$x1.' '.$y1.'H'.$mid.'V'.$y2.'H'.$x2.'" fill="none" stroke="currentColor" marker-end="url(#'.$id.'-a)"/><text x="'.$mid.'" y="'.(($y1+$y2)/2-8).'" text-anchor="middle">'.padChartAttr(padChartFit($label,120)).'</text>';}
 foreach($d['nodes'] as $key=>$n){[$x,$y]=$pos[$key];$svg.='<g><title>'.padChartAttr($n['kind'].': '.$n['label']).'</title>';$fill=' fill="var(--pad-chart-surface,white)" stroke="currentColor"';
 if(in_array($n['kind'],['start','end']))$svg.='<circle cx="'.$x.'" cy="'.$y.'" r="24"'.$fill.' stroke-width="'.($n['kind']==='end'?4:1.5).'"/>';
 elseif($n['kind']==='task')$svg.='<rect x="'.($x-45).'" y="'.($y-25).'" width="90" height="50" rx="8"'.$fill.'/>';
 else $svg.='<path d="M'.$x.' '.($y-30).'L'.($x+30).' '.$y.'L'.$x.' '.($y+30).'L'.($x-30).' '.$y.'Z"'.$fill.'/><text x="'.$x.'" y="'.($y+5).'" text-anchor="middle">'.($n['kind']==='parallel'?'+':'×').'</text>';
 $svg.='<text x="'.$x.'" y="'.($y+48).'" text-anchor="middle">'.padChartAttr(padChartFit($n['label'],135)).'</text></g>';}
 return $svg.'</svg>';
}
