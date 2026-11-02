<?php
// Explicit EIA rack units counted upwards from the bottom, capped at 64 units.
function padDiagramRackParse($text){
 if(strlen($text)>20000)return 'source exceeds 20000 bytes';$units=42;$devices=[];$used=[];$ids=[];
 foreach(preg_split('/\R/',trim($text)) as $i=>$line){$line=trim($line);if($line===''||str_starts_with($line,'#'))continue;
  if(preg_match('/^units ([1-9][0-9]?)$/D',$line,$m)){if($devices||$m[1]>64)return 'units 1..64 must precede devices';$units=(int)$m[1];continue;}
  if(!preg_match('/^device ([A-Za-z][A-Za-z0-9_-]{0,31}) ([1-9][0-9]?) ([1-9][0-9]?) "([^"\r\n]{1,120})"(?: "([^"\r\n]{1,120})")?$/uD',$line,$m))return 'line '.($i+1).': device id bottom-unit height "front label" "rear label"';
  [$all,$id,$start,$height,$front]=$m;$start=(int)$start;$height=(int)$height;if(isset($ids[$id]))return 'duplicate device '.$id;if($start+$height-1>$units)return 'device '.$id.' exceeds rack units';
  for($u=$start;$u<$start+$height;$u++){if(isset($used[$u]))return 'unit '.$u.' overlaps '.$used[$u].' and '.$id;$used[$u]=$id;}
  $ids[$id]=TRUE;$devices[]=[$id,$start,$height,$front,$m[5]??$front];
 }
 return ['units'=>$units,'devices'=>$devices];
}
function padDiagramRackSvg($data,$rear=FALSE,$title='Equipment rack'){
 $u=$data['units'];$width=480;$height=60+$u*22;$desc=[$u.' units, numbered from bottom; '.($rear?'rear':'front').' view'];foreach($data['devices'] as [$id,$start,$span,$front,$back])$desc[]="$id: units $start to ".($start+$span-1).', '.($rear?$back:$front);
 [, $svg]=padChartOpen('diagram-rack',$data,$desc,$title,$width,$height);
 $svg.='<rect x="50" y="25" width="410" height="'.($u*22).'" fill="none" stroke="currentColor"/>';
 for($n=1;$n<=$u;$n++){$y=25+($u-$n)*22;$svg.='<line class="pc-grid" x1="50" x2="460" y1="'.$y.'" y2="'.$y.'"/><text x="40" y="'.($y+15).'" text-anchor="end">'.$n.'</text>';}
 foreach($data['devices'] as [$id,$start,$span,$front,$back]){$y=25+($u-$start-$span+1)*22;$label=$rear?$back:$front;$svg.='<g><title>'.padChartAttr($id.': '.$label).'</title><rect x="59" y="'.($y+2).'" width="392" height="'.($span*22-4).'" rx="3" fill="var(--pad-chart-surface, #eef1f5)" stroke="currentColor"/><text x="255" y="'.($y+$span*11+4).'" text-anchor="middle">'.padChartAttr(padChartFit($label,350)).'</text></g>';}
 return $svg.'</svg>';
}
