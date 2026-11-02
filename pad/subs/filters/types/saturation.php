<?php

  // saturation - `amount` 0..4 (default 1) interpolates encoded sRGB channels around Rec.709 luma (0.2126R + 0.7152G + 0.0722B). Zero gives gray, one is exact identity. This is not a perceptual color-space conversion.
  //
  // {image:thumbnail "photos/harbour.jpg", width=400, filter="saturation", alt="Harbour"/}
  //
  // All alpha bytes remain unchanged. The shared pixel pipeline caps work at 2 megapixels and includes options in derived-image cache identities. Preview and export use the same GD transform.
  //
  // Use `{image:thumbnail "photos/harbour.jpg", width=400, filter="saturation", alt="Harbour"/}`.

// Encoded-sRGB Rec.709 luma interpolation, deliberately not a perceptual color model.
return ['form'=>'image','loops'=>TRUE,'options'=>['amount'=>['number',1,0,4]],
 'apply'=>function($image,$o,$scale){
  $a=$o['amount'];if($a==1)return $image;
  return padFilterMap($image,function($r,$g,$b)use($a){$y=.2126*$r+.7152*$g+.0722*$b;$c=fn($v)=>(int)round(max(0,min(255,$y+$a*($v-$y))));return ($c($r)<<16)|($c($g)<<8)|$c($b);});
 }];
