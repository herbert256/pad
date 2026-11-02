<?php
function padRhythmRead($text,$meter='4/4',$subdivision=4){
 if(!preg_match('~^([1-9]|1[0-2])/(2|4|8|16)$~D',$meter,$m))throw new InvalidArgumentException('Meter must be 1..12 over 2, 4, 8 or 16');
 if(!is_int($subdivision)||$subdivision<1||$subdivision>8)throw new InvalidArgumentException('Subdivision must be 1..8 per beat');
 if(strlen($text)>8000)throw new InvalidArgumentException('Rhythm source exceeds 8000 bytes');
 $beats=(int)$m[1];$length=$beats*$subdivision;$rows=[];
 foreach(preg_split('/\R/',trim($text)) as $line){if(trim($line)==='')continue;if(count($rows)>=16)throw new InvalidArgumentException('At most 16 instruments');
 if(!preg_match('/^([^:]{1,80}):\s*([xX. |]+)$/u',trim($line),$r))throw new InvalidArgumentException('Use Instrument: x... with X accents, x hits and . rests');
 $pattern=str_replace([' ','|'],'',$r[2]);if(strlen($pattern)!==$length)throw new InvalidArgumentException('Each instrument needs exactly '.$length.' subdivisions');$rows[]=[trim($r[1]),$pattern];}
 if(!$rows)throw new InvalidArgumentException('Rhythm needs an instrument');return ['meter'=>$meter,'beats'=>$beats,'subdivision'=>$subdivision,'rows'=>$rows];
}
function padRhythmHtml($d,$title='Rhythm'){
 $e=fn($v)=>htmlspecialchars((string)$v,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');$html='<table class="pad-rhythm"><caption>'.$e($title).' — '.$e($d['meter']).', '.$d['subdivision'].' subdivisions per beat</caption><thead><tr><th scope="col">Instrument</th>';
 for($i=0;$i<$d['beats']*$d['subdivision'];$i++)$html.='<th scope="col">'.(intdiv($i,$d['subdivision'])+1).'.'.($i%$d['subdivision']+1).'</th>';
 $html.='</tr></thead><tbody>';foreach($d['rows'] as [$name,$pattern]){$html.='<tr><th scope="row">'.$e($name).'</th>';foreach(str_split($pattern) as $s)$html.='<td><span aria-label="'.match($s){'X'=>'accent','x'=>'hit',default=>'rest'}.'">'.match($s){'X'=>'●','x'=>'○',default=>'·'}.'</span></td>';$html.='</tr>';}return $html.'</tbody></table>';
}
