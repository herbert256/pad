<?php
// Unsharp mask: original + amount * (original - alpha-aware blur), threshold in RGB units.
return ['form'=>'image','loops'=>TRUE,'options'=>['radius'=>['number',2,1,50],'amount'=>['number',1,0,4],'threshold'=>['number',0,0,255]],
 'apply'=>function($image,$o,$scale){
  if($o['amount']==0)return $image;
  $copy=padFilterResize($image,imagesx($image),imagesy($image));
  $blur=include __DIR__.'/blur.php';$soft=$blur['apply']($copy,['radius'=>$o['radius']],$scale);
  imagealphablending($image,FALSE);imagesavealpha($image,TRUE);
  for($y=0;$y<imagesy($image);$y++)for($x=0;$x<imagesx($image);$x++){
   $p=imagecolorat($image,$x,$y);if(($p>>24)===127)continue;$q=imagecolorat($soft,$x,$y);$out=$p&0x7f000000;
   foreach([16,8,0] as $shift){$a=($p>>$shift)&255;$b=($q>>$shift)&255;$v=abs($a-$b)>=$o['threshold']?$a+$o['amount']*($a-$b):$a;$out|=(int)round(max(0,min(255,$v)))<<$shift;}
   imagesetpixel($image,$x,$y,$out);
  }return $image;
 }];
