<?php
const PAD_SHOGI_START='lnsgkgsnl/1r5b1/ppppppppp/9/9/9/PPPPPPPPP/1B5R1/LNSGKGSNL b - 1';
function padShogiParse($text){
 $text=trim($text);if($text==='start')$text=PAD_SHOGI_START;
 if(strlen($text)>500||!preg_match('/^([PLNSGBRKplnsgbrk1-9+\/]+) ([bw]) (-|[1-9PLNSGBRplnsgbr0-9]+) ([1-9][0-9]{0,5})$/D',$text,$m))return 'expected SFEN placement, b/w, hands and positive move number';
 $ranks=explode('/',$m[1]);if(count($ranks)!==9)return 'nine ranks are required';$pieces=[];$counts=[];$kings=['b'=>0,'w'=>0];
 foreach($ranks as $y=>$rank){$x=0;$chars=str_split($rank);for($i=0;$i<count($chars);$i++){$p=$chars[$i];if(ctype_digit($p)){$x+=(int)$p;continue;}$promoted=$p==='+';if($promoted){$p=$chars[++$i]??'';if(!in_array(strtoupper($p),['P','L','N','S','B','R'],TRUE))return 'only P L N S B R may be promoted';}if(!preg_match('/^[PLNSGBRKplnsgbrk]$/D',$p)||$x>=9)return 'invalid rank';$kind=strtoupper($p);$side=ctype_upper($p)?'b':'w';$counts[$kind]=($counts[$kind]??0)+1;if($kind==='K')$kings[$side]++;$pieces[]=[$x++,$y,$p,$promoted];}if($x!==9)return 'each rank must have nine squares';}
 $hands=[];if($m[3]!=='-'){$rest=$m[3];while($rest!==''){if(!preg_match('/^([1-9][0-9]?)?([PLNSGBRplnsgbr])/',$rest,$h))return 'invalid hand material';$n=($h[1]??'')!==''?(int)$h[1]:1;$p=$h[2];if(isset($hands[$p]))return 'duplicate hand piece';$hands[$p]=$n;$counts[strtoupper($p)]=($counts[strtoupper($p)]??0)+$n;$rest=substr($rest,strlen($h[0]));}}
 if($kings!==['b'=>1,'w'=>1])return 'each side needs one king';foreach(['K'=>2,'R'=>2,'B'=>2,'G'=>4,'S'=>4,'N'=>4,'L'=>4,'P'=>18] as $kind=>$max)if(($counts[$kind]??0)>$max)return 'too many '.$kind.' pieces including hands';
 return ['pieces'=>$pieces,'turn'=>$m[2],'hands'=>$hands,'move'=>(int)$m[4]];
}
function padShogiSvg($position,$flip=FALSE,$size=460,$title='Shogi position'){
 $names=['K'=>'king','R'=>'rook','B'=>'bishop','G'=>'gold','S'=>'silver','N'=>'knight','L'=>'lance','P'=>'pawn'];$body='<rect width="460" height="530" fill="#f2d5a2"/>';$desc=[($position['turn']==='b'?'Black (sente)':'White (gote)').' to move; move '.$position['move']];
 for($i=0;$i<=9;$i++){$v=32+44*$i;$body.='<path d="M32 '.$v.'H428M'.$v.' 32V428" fill="none" stroke="#785630"/>';}
 for($i=0;$i<9;$i++){$body.='<text x="'.(54+44*$i).'" y="23" text-anchor="middle">'.($flip?$i+1:9-$i).'</text><text x="440" y="'.(58+44*$i).'">'.chr(97+($flip?8-$i:$i)).'</text>';}
 foreach($position['pieces'] as [$x,$y,$p,$promoted]){$black=ctype_upper($p);$cx=54+44*($flip?8-$x:$x);$cy=54+44*($flip?8-$y:$y);$turn=($black!==$flip)?0:180;$label=($promoted?'+':'').strtoupper($p);$words=($black?'sente ':'gote ').($promoted?'promoted ':'').$names[strtoupper($p)].' at '.(9-$x).chr(97+$y);$desc[]=$words;$body.='<g transform="translate('.$cx.' '.$cy.') rotate('.$turn.')"><title>'.padNotationAttr($words).'</title><path d="M-16 17L-14 -10L0 -18L14 -10L16 17Z" fill="#fff1cf" stroke="#70502e"/><text x="0" y="7" text-anchor="middle" font-family="sans-serif" font-size="18" fill="'.($promoted?'#a32925':'#222').'">'.$label.'</text></g>';}
 foreach(['b','w'] as $i=>$side){$list=[];foreach($position['hands'] as $p=>$count)if(ctype_upper($p)===($side==='b'))$list[]=strtoupper($p).' × '.$count;$label=($side==='b'?'Sente':'Gote').' hand: '.($list?implode(', ',$list):'empty');$desc[]=$label;$body.='<text x="24" y="'.(464+$i*26).'" font-size="13">'.padNotationAttr($label).'</text>';}
 return '<svg xmlns="http://www.w3.org/2000/svg" class="pad-shogi" width="'.$size.'" viewBox="0 0 460 530" role="img" aria-label="'.padNotationAttr($title).'"><title>'.padNotationAttr($title).'</title><desc>'.padNotationAttr(implode('; ',$desc)).'</desc>'.$body.'</svg>';
}
