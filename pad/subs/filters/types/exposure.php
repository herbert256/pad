<?php

  // exposure - `stops` -8..8 (default 0) multiplies linear-light sRGB intensity by 2^stops, then encodes sRGB and clips. Zero is an exact identity.
  //
  // {image:thumbnail "photos/harbour.jpg", width=400, filter="exposure", alt="Harbour"/}
  //
  // All alpha bytes remain unchanged. The shared pixel pipeline caps work at 2 megapixels and includes options in derived-image cache identities. Preview and export use the same GD transform.
  //
  // Use `{image:thumbnail "photos/harbour.jpg", width=400, filter="exposure", alt="Harbour"/}`.

// Exposure in stops on linear-light sRGB. Alpha is preserved by the shared map.
return ['form'=>'image','loops'=>TRUE,'options'=>['stops'=>['number',0,-8,8]],
 'apply'=>function($image,$o,$scale){
  $gain=2**$o['stops'];
  if($gain==1)return $image;
  $channel=function($c)use($gain){$s=$c/255;$l=$s<=.04045?$s/12.92:(($s+.055)/1.055)**2.4;$l=min(1,$l*$gain);return (int)round(255*($l<=.0031308?12.92*$l:1.055*$l**(1/2.4)-.055));};
  return padFilterMap($image,fn($r,$g,$b)=>($channel($r)<<16)|($channel($g)<<8)|$channel($b));
 }];
