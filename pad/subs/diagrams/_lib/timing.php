<?php
function padDiagramTimingParse($source){
 if(strlen($source)>100000)return 'source exceeds 100 KB';$tracks=[];$marks=[];$ticks=0;
 foreach(preg_split('/\r?\n/',trim($source)) as $line){$line=trim($line);if($line===''||$line==='timing'||str_starts_with($line,'#'))continue;
  if(preg_match('/^mark\s+(\d+)\s+"([^"]*)"$/D',$line,$m)){$marks[]=[(int)$m[1],$m[2]];continue;}
  if(!preg_match('/^([^:]{1,100}):\s*([01xz.\s]+)$/Di',$line,$m))return 'use signal: 0 1 . x z or mark 2 "label"';$states=preg_split('/\s+/',trim(strtolower($m[2])));$last=NULL;
  foreach($states as &$s){if(!in_array($s,['0','1','x','z','.'],TRUE))return 'invalid signal state';if($s==='.'){if($last===NULL)return 'a repeated state needs a previous state';$s=$last;}$last=$s;}unset($s);
  if(count($states)>256)return 'at most 256 ticks';$tracks[]=[trim($m[1]),$states];$ticks=max($ticks,count($states));if(count($tracks)>32)return 'at most 32 tracks';}
 if(count($marks)>64)return 'at most 64 markers';foreach($marks as [$at])if($at>$ticks)return 'marker outside time range';return [$tracks,$marks,$ticks];
}
function padDiagramTimingSvg($data,$tick,$unit,$reverse,$title){
 [$tracks,$marks,$ticks]=$data;if(!$tracks)return '';$left=150;$rowH=60;foreach($tracks as [$name])$rowH=max($rowH,16*count(padDiagramWrap($name,18))+20);$w=$left+40*$ticks+25;$h=85+$rowH*count($tracks);$x=fn($i)=>$left+40*($reverse?$ticks-$i:$i);$desc=[];
 foreach($tracks as [$name,$states])$desc[]=$name.': '.implode(' ',$states);foreach($marks as [$at,$label])$desc[]=$label.' at '.padChartNumber($at*$tick).$unit;
 [, $svg]=padChartOpen('diagram',$data,$desc,$title,$w,$h);$svg.=padDiagramStyle();
 for($i=0;$i<=$ticks;$i++){$xx=$x($i);$svg.='<line class="pc-grid" x1="'.$xx.'" x2="'.$xx.'" y1="35" y2="'.($h-25).'"/><text x="'.$xx.'" y="'.($h-6).'" text-anchor="middle">'.padChartAttr(padChartNumber($i*$tick).$unit).'</text>';}
 foreach($marks as [$at,$label]){$xx=$x($at);$svg.='<line class="pd-edge pd-dashed" x1="'.$xx.'" x2="'.$xx.'" y1="22" y2="'.($h-25).'"/>'.padDiagramText([padChartFit($label,180)],$xx,15);}
 foreach($tracks as $i=>[$name,$states]){$top=50+$rowH*$i;$base=$top+32;$svg.=padDiagramLines(padDiagramWrap($name,18),$left-10,$top+16,'','end');$last=NULL;
  foreach($states as $j=>$s){$xx=$x($j);$end=$x($j+1);$y=$s==='1'?$top:($s==='0'?$base:$top+16);if($last!==NULL&&$last!==$y)$svg.='<line class="pd-edge" x1="'.$xx.'" x2="'.$xx.'" y1="'.$last.'" y2="'.$y.'"/>';
   if($s==='x')$svg.='<path class="pd-edge" d="M'.$xx.' '.$top.'L'.$end.' '.$base.'M'.$xx.' '.$base.'L'.$end.' '.$top.'"/>';
   else $svg.='<line class="pd-edge'.($s==='z'?' pd-dashed':'').'" x1="'.$xx.'" x2="'.$end.'" y1="'.$y.'" y2="'.$y.'"><title>'.padChartAttr($name.': '.$s.' at '.padChartNumber($j*$tick).$unit).'</title></line>';$last=$y;}}
 return $svg.'</svg>';
}
