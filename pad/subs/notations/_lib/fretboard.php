<?php
include_once PAD.'subs/notations/_lib/music.php';
function padFretboardParse($text,$tuning){
 $scales=['major'=>[0,2,4,5,7,9,11],'minor'=>[0,2,3,5,7,8,10],'pentatonic'=>[0,2,4,7,9],'minor-pentatonic'=>[0,3,5,7,10],'dorian'=>[0,2,3,5,7,9,10],'chromatic'=>range(0,11)];
 if(!preg_match('/^([A-Ga-g](?:#|b)?)\s+(major|minor|pentatonic|minor-pentatonic|dorian|chromatic)$/D',trim($text),$m))return 'use a root and scale, for example C major or A minor-pentatonic';
 $root=padMusicNote($m[1]);$strings=[];
 foreach(preg_split('/[\s,]+/',trim($tuning)) as $one){$note=padMusicNote($one);if(!$note)return 'invalid tuning note';$strings[]=$note;}
 if(count($strings)<1||count($strings)>12)return 'tuning needs 1 to 12 strings';
 return [$root,$scales[$m[2]],$strings];
}
function padFretboardSvg($spec,$start,$frets,$labels,$flip,$size,$title){
 [$root,$intervals,$strings]=$spec;$w=100+48*($frets+1);$h=65+32*count($strings);$body='';$desc=[];$intervalNames=['1','b2','2','b3','3','4','b5','5','b6','6','b7','7'];
 $x=fn($f)=>60+48*($flip?$frets-($f-$start):$f-$start);
 foreach($strings as $i=>$string){$y=35+32*(count($strings)-1-$i);$body.='<line x1="36" x2="'.($w-40).'" y1="'.$y.'" y2="'.$y.'" stroke="currentColor"/><text x="16" y="'.($y+5).'">'.padNotationAttr($string[2]).'</text>';
  for($f=$start;$f<=$start+$frets;$f++){$pitch=($string[0]+$f)%12;$interval=($pitch-$root[0]+12)%12;if(!in_array($interval,$intervals,TRUE))continue;$text=$labels==='intervals'?$intervalNames[$interval]:padMusicName($pitch,$root[2]);$desc[]='string '.($i+1).', fret '.$f.': '.padMusicName($pitch,$root[2]);$body.='<circle cx="'.$x($f).'" cy="'.$y.'" r="12" fill="'.($interval===0?'#973e2a':'#285d82').'"/><text x="'.$x($f).'" y="'.($y+4).'" text-anchor="middle" fill="#fff" font-size="11">'.padNotationAttr($text).'</text>';}}
 for($f=$start;$f<=$start+$frets;$f++)$body='<line x1="'.($x($f)+($flip?-24:24)).'" x2="'.($x($f)+($flip?-24:24)).'" y1="20" y2="'.($h-52).'" stroke="currentColor" opacity="0.35"/>'.$body.'<text x="'.$x($f).'" y="'.($h-20).'" text-anchor="middle">'.$f.'</text>';
 return '<svg xmlns="http://www.w3.org/2000/svg" class="pad-fretboard" role="img" aria-label="'.padNotationAttr($title).'" width="'.$size.'" viewBox="0 0 '.$w.' '.$h.'"><title>'.padNotationAttr($title).'</title><desc>'.padNotationAttr(implode('; ',$desc)).'</desc>'.$body.'</svg>';
}
