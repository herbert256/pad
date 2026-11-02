<?php
include_once PAD.'subs/diagrams/_lib/boxes.php';
function padDiagramBlockParse($source){
 if(strlen($source)>100000)return 'source exceeds 100 KB';$nodes=[];$edges=[];$occupied=[];
 foreach(preg_split('/\r?\n/',trim($source)) as $line){$line=trim($line);if($line===''||$line==='block'||str_starts_with($line,'#'))continue;
  if(preg_match('/^([A-Za-z][\w-]*)\s*->\s*([A-Za-z][\w-]*)(?:\s*:\s*(.*))?$/D',$line,$m)){$edges[]=[$m[1],$m[2],$m[3]??''];if(count($edges)>128)return 'at most 128 edges';continue;}
  if(!preg_match('/^([A-Za-z][\w-]*)\s+"([^"]*)"\s+at\s+(\d+),(\d+)(?:\s+span\s+(\d+),(\d+))?$/D',$line,$m))return 'use id "label" at column,row span width,height';$id=$m[1];if(isset($nodes[$id]))return 'duplicate node';[$x,$y,$w,$h]=[(int)$m[3],(int)$m[4],(int)($m[5]??1),(int)($m[6]??1)];if(min($x,$y,$w,$h)<1||$x+$w>17||$y+$h>17)return 'grid must fit 16 by 16';
  for($a=$x;$a<$x+$w;$a++)for($b=$y;$b<$y+$h;$b++){if(isset($occupied["$a,$b"]))return 'overlapping blocks';$occupied["$a,$b"]=TRUE;}$nodes[$id]=[$m[2],$x-1,$y-1,$w,$h];if(count($nodes)>64)return 'at most 64 nodes';}
 foreach($edges as [$a,$b])if(!isset($nodes[$a],$nodes[$b]))return 'edge references unknown node';return [$nodes,$edges];
}
function padDiagramBlockSvg($data,$direction,$title){
 [$nodes,$edges]=$data;if(!$nodes)return '';$boxes=[];$cols=$rows=0;
 foreach($nodes as $id=>[$label,$x,$y,$w,$h]){if(in_array($direction,['LR','RL'],TRUE)){[$x,$y]=[$y,$x];[$w,$h]=[$h,$w];}$cols=max($cols,$x+$w);$rows=max($rows,$y+$h);$boxes[$id]=['label'=>$label,'x'=>$x,'y'=>$y,'w'=>$w,'h'=>$h];}
 foreach($boxes as &$b){if($direction==='RL')$b['x']=$cols-$b['x']-$b['w'];if($direction==='BT')$b['y']=$rows-$b['y']-$b['h'];$b['x']=40+$b['x']*220;$b['y']=40+$b['y']*120;$b['w']=$b['w']*220-60;$b['h']=$b['h']*120-50;}unset($b);
 $desc=[];foreach($nodes as $id=>$n)$desc[]=$id.': '.$n[0].', column '.($n[1]+1).', row '.($n[2]+1).', span '.$n[3].' by '.$n[4];foreach($edges as [$a,$b,$l])$desc[]=$a.' to '.$b.': '.$l;
 [$id,$svg]=padChartOpen('diagram',$data,$desc,$title,40+$cols*220,40+$rows*120);return $svg.padDiagramStyle().padDiagramBoxEdges($boxes,$edges,$id).padDiagramBoxNodes($boxes).'</svg>';
}
