<?php
// Exposure in stops on linear-light sRGB. Alpha is preserved by the shared map.
return ['form'=>'image','loops'=>TRUE,'options'=>['stops'=>['number',0,-8,8]],
 'apply'=>function($image,$o,$scale){
  $gain=2**$o['stops'];
  if($gain==1)return $image;
  $channel=function($c)use($gain){$s=$c/255;$l=$s<=.04045?$s/12.92:(($s+.055)/1.055)**2.4;$l=min(1,$l*$gain);return (int)round(255*($l<=.0031308?12.92*$l:1.055*$l**(1/2.4)-.055));};
  return padFilterMap($image,fn($r,$g,$b)=>($channel($r)<<16)|($channel($g)<<8)|$channel($b));
 }];
