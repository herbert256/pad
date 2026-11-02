<?php
// Encoded-sRGB Rec.709 luma interpolation, deliberately not a perceptual color model.
return ['form'=>'image','loops'=>TRUE,'options'=>['amount'=>['number',1,0,4]],
 'apply'=>function($image,$o,$scale){
  $a=$o['amount'];if($a==1)return $image;
  return padFilterMap($image,function($r,$g,$b)use($a){$y=.2126*$r+.7152*$g+.0722*$b;$c=fn($v)=>(int)round(max(0,min(255,$y+$a*($v-$y))));return ($c($r)<<16)|($c($g)<<8)|$c($b);});
 }];
