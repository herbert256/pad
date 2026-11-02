<?php
include_once PAD.'subs/diagrams/_lib/boxes.php';
function padDiagramC4Parse($source){
 if(strlen($source)>100000)return 'source exceeds 100 KB';$nodes=[];$edges=[];$stack=[''];
 foreach(preg_split('/\r?\n/',trim($source)) as $line){$line=trim($line);if($line===''||$line==='c4'||str_starts_with($line,'#'))continue;
  if($line==='}'){if(count($stack)===1)return 'unmatched boundary close';array_pop($stack);continue;}
  if(preg_match('/^([A-Za-z][\w-]*)\s*->\s*([A-Za-z][\w-]*)(?:\s*:\s*(.*))?$/D',$line,$m)){$edges[]=[$m[1],$m[2],$m[3]??''];if(count($edges)>128)return 'at most 128 relationships';continue;}
  if(!preg_match('/^(boundary|person|system|container)\s+([A-Za-z][\w-]*)\s+"([^"]*)"(?:\s+"([^"]*)")?\s*(\{)?$/D',$line,$m))return 'use person/system/container id "label" or boundary id "label" {';
  [$kind,$id,$label]=[$m[1],$m[2],$m[3]];if(isset($nodes[$id]))return 'duplicate element';if(($kind==='boundary')!==isset($m[5]))return 'only boundaries open braces';$nodes[$id]=['kind'=>$kind,'label'=>$label,'technology'=>$m[4]??'','parent'=>end($stack)];if(count($nodes)>64)return 'at most 64 elements';
  if($kind==='boundary'){$stack[]=$id;if(count($stack)>5)return 'at most four nested boundaries';}}
 if(count($stack)!==1)return 'unclosed boundary';foreach($edges as [$a,$b])if(!isset($nodes[$a],$nodes[$b]))return 'relationship references unknown element';return [$nodes,$edges];
}
function padDiagramC4Svg($data,$direction,$title){
 [$nodes,$edges]=$data;if(!$nodes)return '';$children=[];foreach($nodes as $id=>$n)$children[$n['parent']][]=$id;$sizes=[];$horizontal=in_array($direction,['LR','RL'],TRUE);
 $measure=function($id)use(&$measure,&$sizes,$nodes,$children,$horizontal){
  if($id!==''&&$nodes[$id]['kind']!=='boundary')return $sizes[$id]=[240,110];$kids=$children[$id]??[];$cw=240;$ch=110;foreach($kids as $k){[$w,$h]=$measure($k);$cw=max($cw,$w);$ch=max($ch,$h);}$n=max(1,count($kids));$cols=$horizontal?(int)ceil($n/2):min(2,$n);$rows=$horizontal?min(2,$n):(int)ceil($n/2);return $sizes[$id]=[$cols*$cw+max(0,$cols-1)*70+100,$rows*$ch+max(0,$rows-1)*70+100,$cw,$ch,$cols];};
 [$width,$height]=$measure('');$boxes=[];$boundaries=[];
 $place=function($id,$x,$y)use(&$place,&$boxes,&$boundaries,$sizes,$nodes,$children){
  [$w,$h]=$sizes[$id];if($id!==''){$n=$nodes[$id];$label=$n['label'].' ['.$n['kind'].']'.($n['technology']!==''?' — '.$n['technology']:'');$box=['x'=>$x,'y'=>$y,'w'=>$w,'h'=>$h,'label'=>$label];if($n['kind']==='boundary')$boundaries[$id]=$box;else $boxes[$id]=$box;}
  if(!isset($sizes[$id][2]))return;[$w,$h,$cw,$ch,$cols]=$sizes[$id];foreach($children[$id]??[] as $i=>$child)$place($child,$x+50+($i%$cols)*($cw+70),$y+50+intdiv($i,$cols)*($ch+70));};$place('',0,0);
 foreach([&$boxes,&$boundaries] as &$set)foreach($set as &$b){if($direction==='RL')$b['x']=$width-$b['x']-$b['w'];if($direction==='BT')$b['y']=$height-$b['y']-$b['h'];}unset($set,$b);
 $desc=[];foreach($nodes as $id=>$n)$desc[]=$id.': '.$n['kind'].' '.$n['label'].($n['technology']!==''?', '.$n['technology']:'').($n['parent']!==''?', inside '.$n['parent']:'');foreach($edges as [$a,$b,$l])$desc[]=$a.' to '.$b.': '.$l;
 [$id,$svg]=padChartOpen('diagram',$data,$desc,$title,$width,$height);$svg.=padDiagramStyle();
 foreach($boundaries as $b)$svg.='<rect class="pd-frame pd-dashed" x="'.$b['x'].'" y="'.$b['y'].'" width="'.$b['w'].'" height="'.$b['h'].'" rx="8"/>'.padDiagramText([padChartFit($b['label'],$b['w']-20)],$b['x']+$b['w']/2,$b['y']+20);
 return $svg.padDiagramBoxEdges($boxes+$boundaries,$edges,$id).padDiagramBoxNodes($boxes).'</svg>';
}
