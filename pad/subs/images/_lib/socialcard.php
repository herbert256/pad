<?php
// Deterministic local PNG composition. Font and coverage are packaged under SIL OFL.
function padSocialCardText($text,$limit){
 if(!is_string($text)||!mb_check_encoding($text,'UTF-8')||mb_strlen($text)>$limit)return 'expected UTF-8 text of at most '.$limit.' characters';
 if(preg_match('/[\x00-\x08\x0b\x0c\x0e-\x1f\x7f]/',$text))return 'control characters are not supported';
 // GD FreeType has no complex-text shaper. Fail explicitly instead of producing wrong text.
 if(preg_match('/[\p{Arabic}\p{Hebrew}\p{Devanagari}\p{Bengali}\p{Thai}]/u',$text))return 'this font renderer supports unshaped Latin, Greek and Cyrillic text; complex-script shaping is unavailable';
 static $ranges=NULL;$ranges??=json_decode(file_get_contents(PAD.'subs/images/fonts/NotoSans-coverage.json'),TRUE);
 foreach(preg_split('//u',$text,-1,PREG_SPLIT_NO_EMPTY) as $char){$cp=mb_ord($char);if(in_array($cp,[9,10,13],TRUE))continue;$found=FALSE;foreach($ranges as [$a,$b])if($cp>=$a&&$cp<=$b){$found=TRUE;break;}if(!$found)return sprintf('packaged Noto Sans has no glyph for U+%04X',$cp);}
 return NULL;
}
function padSocialCardLines($text,$font,$size,$width){
 $lines=[];$line='';$text=preg_replace('/\s+/u',' ',trim($text));
 // Character wrapping keeps long words and URLs bounded as well as ordinary prose.
 foreach(preg_split('//u',$text,-1,PREG_SPLIT_NO_EMPTY) as $char){$next=$line.$char;$box=imagettfbbox($size,0,$font,$next);$w=max($box[0],$box[2],$box[4],$box[6])-min($box[0],$box[2],$box[4],$box[6]);if($w>$width&&$line!==''){$at=mb_strrpos($line,' ');if($at!==FALSE&&$at>mb_strlen($line)/2){$lines[]=rtrim(mb_substr($line,0,$at));$line=ltrim(mb_substr($line,$at+1).$char);}else{$lines[]=rtrim($line);$line=ltrim($char);}}else $line=$next;}
 if($line!=='')$lines[]=rtrim($line);return $lines;
}
function padSocialCardLayout($title,$description,$w,$h){
 $font=PAD.'subs/images/fonts/NotoSans-Regular.ttf';$margin=(int)round($w*.06);$usable=$w-2*$margin;$titleSize=$w*.041;$descSize=$w*.021;
 do{$titleLines=padSocialCardLines($title,$font,$titleSize,$usable);$titleSize-=1;}while(count($titleLines)>3&&$titleSize>$w*.022);$titleSize++;
 $descLines=padSocialCardLines($description,$font,$descSize,$usable);
 if(count($titleLines)>3||count($descLines)>4)return 'text does not fit the card; shorten title or description';
 $start=(int)round($h*.29);$end=$start+count($titleLines)*$titleSize*1.55+count($descLines)*$descSize*1.6+24;
 if($end>$h-$margin)return 'text does not fit the selected card dimensions';
 return compact('font','margin','titleSize','descSize','titleLines','descLines','start');
}
function padSocialCardImage($title,$description,$brand,$w,$h,$logo=NULL){
 $layout=padSocialCardLayout($title,$description,$w,$h);if(is_string($layout))return $layout;
 $im=imagecreatetruecolor($w,$h);$bg=imagecolorallocate($im,243,240,231);$ink=imagecolorallocate($im,21,43,58);$muted=imagecolorallocate($im,65,82,92);$accent=imagecolorallocate($im,26,127,115);imagefill($im,0,0,$bg);imagefilledrectangle($im,0,0,16,$h,$accent);
 $m=$layout['margin'];$font=$layout['font'];$brandX=$m;
 if($logo){[$path,$name,$type,$sw,$sh,$turn]=$logo;[$crop,$lw,$lh]=padImgSizes($sw,$sh,(int)round($w*.067),(int)round($w*.067),'contain');$pic=padImgDraw($path,$type,$turn,$crop,$lw,$lh,FALSE);if(!$pic)return 'cannot decode local logo';imagecopy($im,$pic,$m,$m,0,0,$lw,$lh);padImgDraw(NULL);$brandX+=$lw+20;}
 if($brand!=='')imagettftext($im,$w*.019,0,$brandX,$m+(int)round($w*.028),$accent,$font,$brand);
 $y=$layout['start'];foreach($layout['titleLines'] as $line){imagettftext($im,$layout['titleSize'],0,$m,(int)round($y),$ink,$font,$line);$y+=$layout['titleSize']*1.55;}$y+=20;
 foreach($layout['descLines'] as $line){imagettftext($im,$layout['descSize'],0,$m,(int)round($y),$muted,$font,$line);$y+=$layout['descSize']*1.6;}
 return $im;
}
function padImageSocialCard($o){
 if(!function_exists('imagettftext'))return padSocialCardError('GD with FreeType is required');
 foreach(['title'=>180,'description'=>400,'brand'=>60] as $name=>$limit){if($error=padSocialCardText($o[$name],$limit))return padSocialCardError($name.': '.$error);}
 if(trim($o['title'])==='')return padSocialCardError('title is required');if($o['font']!=='noto-sans')return padSocialCardError('font must be noto-sans (packaged)');
 foreach(['width'=>[600,1600],'height'=>[315,900]] as $name=>[$min,$max])if(!is_scalar($o[$name])||!preg_match('/^[0-9]+$/D',(string)$o[$name])||$o[$name]<$min||$o[$name]>$max)return padSocialCardError("$name must be an integer from $min to $max");
 $w=(int)$o['width'];$h=(int)$o['height'];if($w*$h>1440000)return padSocialCardError('at most 1.44 megapixels');
 if(!is_string($o['logo'])||!is_string($o['alt']))return padSocialCardError('logo and alt must be text');
 $logo=$o['logo']!==''?padImgSource($o['logo']):NULL;if($o['logo']!==''&&!$logo)return '';
 if($logo&&$logo[3]*$logo[4]>4000000)return padSocialCardError('logo may contain at most 4 megapixels');
 if($error=padSocialCardLayout($o['title'],$o['description'],$w,$h))if(is_string($error))return padSocialCardError($error);
 $font=PAD.'subs/images/fonts/NotoSans-Regular.ttf';if(count(padSocialCardLines($o['brand'],$font,$w*.019,$w*.88-($logo?$w*.067+20:0)))>1)return padSocialCardError('brand does not fit on one line');$base=['socialcard-v1',$o,hash_file('sha256',$font),$logo?hash_file('sha256',$logo[0]):''];
 [$file,$url,$hash]=padImgName($base,$w,$h,'png');
 if(!is_file($file)){if(!padImgDir())return '';$im=padSocialCardImage($o['title'],$o['description'],$o['brand'],$w,$h,$logo);if(is_string($im))return padSocialCardError($im);$temp=dirname($file).'/.'.$hash.'-'.bin2hex(random_bytes(4));if(!imagepng($im,$temp,6)||!@rename($temp,$file)){@unlink($temp);return padSocialCardError('cannot write card image');}}
 $alt=$o['alt']!==''?$o['alt']:$o['title'].($o['description']!==''?'. '.$o['description']:'');
 return '<figure class="pad-image-socialcard">'.padImgTag($url,'',$w,$h,$alt).'<figcaption>'.padImgAttr($o['title']).' <a download href="'.padImgAttr($url).'">Download PNG</a></figcaption></figure>';
}
function padSocialCardError($message){if($GLOBALS['padCheckSyntax']??FALSE)padError('the socialcard image: '.$message);return '';}
