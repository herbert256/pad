<?php
const PAD_BACKGAMMON_START='1:B2 6:W5 8:W3 12:B5 13:W5 17:B3 19:B5 24:W2; bar:W0,B0; off:W0,B0; cube:1,C; turn:W';
function padBackgammonParse($text){
 if(trim($text)==='start')$text=PAD_BACKGAMMON_START;$parts=explode(';',trim($text));$points=[];$counts=['W'=>0,'B'=>0];$spec=['bar'=>['W'=>0,'B'=>0],'off'=>['W'=>0,'B'=>0],'cube'=>[1,'C'],'turn'=>'W'];$seen=[];
 foreach(preg_split('/\s+/',trim(array_shift($parts))) as $p){if(!preg_match('/^([1-9]|1[0-9]|2[0-4]):([WB])([1-9]|1[0-5])$/D',$p,$m))return 'point must be 1..24:W1..15 or B1..15';$n=(int)$m[1];if(isset($points[$n]))return 'duplicate point';$points[$n]=[$m[2],(int)$m[3]];$counts[$m[2]]+=(int)$m[3];}
 foreach($parts as $p){$p=trim($p);if(!preg_match('/^(bar|off|cube|turn):(.*)$/D',$p,$m))return 'unknown field';if(isset($seen[$m[1]]))return 'duplicate field';$seen[$m[1]]=TRUE;
  if($m[1]==='bar'||$m[1]==='off'){if(!preg_match('/^W(0|[1-9]|1[0-5]),B(0|[1-9]|1[0-5])$/D',$m[2],$v))return 'bar/off need Wcount,Bcount';$spec[$m[1]]=['W'=>(int)$v[1],'B'=>(int)$v[2]];$counts['W']+=(int)$v[1];$counts['B']+=(int)$v[2];}
  elseif($m[1]==='cube'){if(!preg_match('/^(1|2|4|8|16|32|64),(C|W|B)$/D',$m[2],$v))return 'cube needs a power of two 1..64 and owner C, W or B';$spec['cube']=[(int)$v[1],$v[2]];}
  else {if(!in_array($m[2],['W','B'],TRUE))return 'turn must be W or B';$spec['turn']=$m[2];}}
 if($counts['W']!==15||$counts['B']!==15)return 'each side must have fifteen checkers including bar and off';ksort($points);return [$points,$spec];
}
function padBackgammonSvg($position,$flip,$size,$title){
 [$points,$s]=$position;$body='<rect width="640" height="390" rx="8" fill="#ba874e"/><rect x="12" y="25" width="616" height="320" fill="#f3d5a1"/><rect x="302" y="25" width="36" height="320" fill="#76502d"/>';$desc=['Turn '.$s['turn']];
 for($n=1;$n<=24;$n++){$display=$flip?25-$n:$n;$top=$display>=13;$i=$top?$display-13:12-$display;$x=36+47*$i+($i>=6?20:0);$y=$top?28:342;$tip=$top?160:210;$body.='<path d="M'.($x-21).' '.$y.'L'.($x+21).' '.$y.'L'.$x.' '.$tip.'Z" fill="'.($n%2?'#7d3f32':'#d69c56').'"/><text x="'.$x.'" y="'.($top?18:362).'" text-anchor="middle" fill="#1d1711">'.$n.'</text>';
  if(!isset($points[$n]))continue;[$side,$count]=$points[$n];$words='point '.$n.': '.$side.' '.$count;$desc[]=$words;for($i=0;$i<min(5,$count);$i++){$cy=$y+($top?1:-1)*(17+$i*26);$body.='<circle cx="'.$x.'" cy="'.$cy.'" r="15" fill="'.($side==='W'?'#fff8df':'#292622').'" stroke="#66503b"><title>'.$words.'</title></circle>';if($i===min(5,$count)-1&&$count>5)$body.='<text x="'.$x.'" y="'.($cy+5).'" text-anchor="middle" fill="'.($side==='W'?'#222':'#fff').'">'.$count.'</text>';}}
 $summary='Bar W'.$s['bar']['W'].' B'.$s['bar']['B'].'; Off W'.$s['off']['W'].' B'.$s['off']['B'].'; Cube '.$s['cube'][0].' owner '.$s['cube'][1].'; Turn '.$s['turn'];$desc[]=$summary;$body.='<text x="320" y="383" text-anchor="middle" fill="#201810" font-size="13">'.padNotationAttr($summary).'</text>';
 foreach(['W','B'] as $i=>$side)if($s['bar'][$side])$body.='<circle cx="320" cy="'.(120+$i*110).'" r="15" fill="'.($side==='W'?'#fff8df':'#292622').'"/><text x="320" y="'.(125+$i*110).'" text-anchor="middle" fill="'.($side==='W'?'#222':'#fff').'">'.$s['bar'][$side].'</text>';
 $body.='<rect x="304" y="175" width="32" height="30" rx="3" fill="#fff" stroke="#222"/><text x="320" y="196" text-anchor="middle" fill="#222">'.$s['cube'][0].'</text>';
 return '<svg xmlns="http://www.w3.org/2000/svg" class="pad-backgammon" width="'.$size.'" viewBox="0 0 640 390" role="img" aria-label="'.padNotationAttr($title).'"><title>'.padNotationAttr($title).'</title><desc>'.padNotationAttr(implode('; ',$desc)).'</desc>'.$body.'</svg>';
}
