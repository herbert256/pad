<?php
include_once PAD.'subs/images/_lib/socialcard.php';
include_once PAD.'subs/filters/_lib/filter.php';
function padWatermarkCompose($image,$mark,$opacity,$position,$scale,$margin=8){
 $w=imagesx($image);$h=imagesy($image);$mw=max(1,(int)round(($w-2*$margin)*$scale));$mh=max(1,(int)round(imagesy($mark)*$mw/imagesx($mark)));
 if($mh>$h-2*$margin){$mh=max(1,$h-2*$margin);$mw=max(1,(int)round(imagesx($mark)*$mh/imagesy($mark)));}
 $stamp=padFilterResize($mark,$mw,$mh);imagealphablending($stamp,FALSE);imagesavealpha($stamp,TRUE);
 for($y=0;$y<$mh;$y++)for($x=0;$x<$mw;$x++){$p=imagecolorat($stamp,$x,$y);$a=(int)round(127-(127-(($p>>24)&127))*$opacity);imagesetpixel($stamp,$x,$y,($a<<24)|($p&0xffffff));}
 $x=str_contains($position,'left')?$margin:(str_contains($position,'right')?$w-$mw-$margin:(int)floor(($w-$mw)/2));
 $y=str_contains($position,'top')?$margin:(str_contains($position,'bottom')?$h-$mh-$margin:(int)floor(($h-$mh)/2));
 imagealphablending($image,TRUE);imagesavealpha($image,TRUE);imagecopy($image,$stamp,$x,$y,0,0,$mw,$mh);return $image;
}
function padImageWatermark($file,$o){
 $text=$o['text']??'';$markFile=$o['mark']??'';
 if(!is_string($text)||!is_string($markFile)||($text==='')===($markFile===''))throw new InvalidArgumentException('Watermark needs either text or a local mark');
 if(!is_string($o['alt']??NULL))throw new InvalidArgumentException('Watermark requires alt text');
 $position=$o['position']??'bottom-right';if(!in_array($position,['top-left','top-right','bottom-left','bottom-right','center'],TRUE))throw new InvalidArgumentException('Unknown watermark position');
 $opacity=$o['opacity']??.5;$scale=$o['scale']??.25;
 foreach(['opacity'=>[$opacity,0,1],'scale'=>[$scale,.05,1],'width'=>[$o['width']??600,32,2000],'height'=>[$o['height']??0,0,2000]] as $k=>[$v,$lo,$hi])if(!is_numeric($v)||!is_finite((float)$v)||$v<$lo||$v>$hi)throw new InvalidArgumentException('Invalid watermark '.$k);
 $source=padImgSource($file);if(!$source)return '';[$path,$name,$type,$sw,$sh,$turn]=$source;
 [$crop,$w,$h]=padImgSizes($sw,$sh,(int)($o['width']??600),(int)($o['height']??0),'contain');
 if($w*$h>2000000||$w<20||$h<20)throw new InvalidArgumentException('Watermark output requires 20px sides and at most 2 megapixels');
 $font=PAD.'subs/images/fonts/NotoSans-Regular.ttf';$identity='';
 if($text!==''){$problem=padSocialCardText($text,200);if($problem!==NULL)throw new InvalidArgumentException($problem);$box=imagettfbbox(32,0,$font,$text);$tw=max($box[0],$box[2],$box[4],$box[6])-min($box[0],$box[2],$box[4],$box[6]);$mark=imagecreatetruecolor(max(1,$tw+8),52);imagealphablending($mark,FALSE);imagesavealpha($mark,TRUE);imagefill($mark,0,0,0x7f000000);imagettftext($mark,32,0,4,38,0xffffff,$font,$text);$identity=hash_file('sha256',$font);}
 else{$m=padImgSource($markFile);if(!$m)return '';if($m[3]*$m[4]>4000000)throw new InvalidArgumentException('Watermark mark exceeds 4 megapixels');$mark=padImgDraw($m[0],$m[2],$m[5],[0,0,$m[3],$m[4]],$m[3],$m[4],FALSE);$identity=padImgHash($m[0]);}
 $spec=['apply'=>fn($im,$opts,$density)=>padWatermarkCompose($im,$mark,(float)$opacity,$position,(float)$scale)];
 $base=['watermark-v1',padImgHash($path),$turn,$crop,$text,$identity,$position,(float)$opacity,(float)$scale];
 try{$url=padImgThumb($path,$type,$turn,$crop,$w,$h,'png',100,$base,[['watermark',$spec,[]]],1);return $url?padImgTag($url,'',$w,$h,$o['alt']):'';}finally{padImgDraw(NULL);}
}
