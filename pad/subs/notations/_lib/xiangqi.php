<?php
const PAD_XIANGQI_START='rnbakabnr/9/1c5c1/p1p1p1p1p/9/9/P1P1P1P1P/1C5C1/9/RNBAKABNR w';
function padXiangqiParse($text){
 if(trim($text)==='start')$text=PAD_XIANGQI_START;
 if(!preg_match('/^([rnbakcpRNBAKCP1-9\/]+)(?:\s+([wb]))?$/D',trim($text),$m))return 'expected a placement and optional w or b';
 $ranks=explode('/',$m[1]);if(count($ranks)!==10)return 'expected ten ranks';$pieces=[];$counts=[];
 foreach($ranks as $y=>$rank){$x=0;foreach(str_split($rank) as $p){if(ctype_digit($p)){$x+=(int)$p;continue;}if($x>8)return 'rank wider than nine';$side=ctype_upper($p)?'red':'black';$type=strtolower($p);$counts[$side][$type]=($counts[$side][$type]??0)+1;
  if(in_array($type,['k','a'],TRUE)&&($x<3||$x>5||($side==='red'?$y<7:$y>2)))return 'general or advisor outside palace';
  if($type==='b'&&($side==='red'?$y<5:$y>4))return 'elephant across river';
  $pieces[]=[$x,$y,$p];$x++;}if($x!==9)return 'every rank must contain nine intersections';}
 foreach(['red','black'] as $side){if(($counts[$side]['k']??0)!==1)return 'each side needs one general';foreach(['r'=>2,'n'=>2,'b'=>2,'a'=>2,'c'=>2,'p'=>5] as $p=>$max)if(($counts[$side][$p]??0)>$max)return 'too many '.$side.' pieces';}
 return [$pieces,($m[2]??'w')==='w'?'red':'black'];
}
function padXiangqiSvg($position,$flip,$size,$title){
 [$pieces,$turn]=$position;$glyph=['r'=>'車','n'=>'馬','b'=>'象','a'=>'士','k'=>'將','c'=>'砲','p'=>'卒','R'=>'車','N'=>'馬','B'=>'相','A'=>'仕','K'=>'帥','C'=>'炮','P'=>'兵'];$names=['r'=>'chariot','n'=>'horse','b'=>'elephant','a'=>'advisor','k'=>'general','c'=>'cannon','p'=>'soldier'];$body='<rect width="420" height="470" fill="#f4d9aa"/>';$desc=[$turn.' to move'];
 for($y=0;$y<10;$y++)$body.='<path d="M30 '.(30+45*$y).'H390" stroke="#533b24"/>';
 for($x=0;$x<9;$x++)$body.='<path d="M'.(30+45*$x).' 30V210M'.(30+45*$x).' 255V435" stroke="#533b24"/>';
 $body.='<path d="M165 30L255 120M255 30L165 120M165 345L255 435M255 345L165 435M30 210V255M390 210V255" stroke="#533b24" fill="none"/><text x="210" y="240" text-anchor="middle" fill="#533b24">楚河　漢界</text>';
 for($i=0;$i<9;$i++)$body.='<text x="'.(30+$i*45).'" y="463" text-anchor="middle" fill="#222">'.chr(97+($flip?8-$i:$i)).'</text>';
 for($i=0;$i<10;$i++)$body.='<text x="8" y="'.(35+$i*45).'" fill="#222">'.($flip?$i:9-$i).'</text>';
 foreach($pieces as [$x,$y,$p]){$red=ctype_upper($p);$words=($red?'red ':'black ').$names[strtolower($p)].' at '.chr(97+$x).(9-$y);$desc[]=$words;$xx=30+45*($flip?8-$x:$x);$yy=30+45*($flip?9-$y:$y);$color=$red?'#a32925':'#222';$body.='<g><title>'.padNotationAttr($words).'</title><circle cx="'.$xx.'" cy="'.$yy.'" r="19" fill="#fff0cc" stroke="'.$color.'" stroke-width="2"/><text x="'.$xx.'" y="'.($yy+8).'" text-anchor="middle" fill="'.$color.'" font-size="25">'.$glyph[$p].'</text></g>';}
 return '<svg xmlns="http://www.w3.org/2000/svg" class="pad-xiangqi" width="'.$size.'" viewBox="0 0 420 470" role="img" aria-label="'.padNotationAttr($title).'"><title>'.padNotationAttr($title).'</title><desc>'.padNotationAttr(implode('; ',$desc)).'</desc>'.$body.'</svg>';
}
