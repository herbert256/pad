<?php
// median - 3x3 channel median, edge coordinates clamped. Alpha is the centre pixel's
// alpha: transparent neighbours cannot spread their colour into an opaque centre.
//
//   {image:thumbnail 'photos/harbour.jpg', width=400, filter='median', alt='The harbour'/}
//
// The nine RGB samples are sorted separately; the middle sample becomes the output.
return ['form'=>'image','loops'=>TRUE,'pixels'=>TRUE,'options'=>[],
 'apply'=>function($image,$o,$scale){
  $w=imagesx($image);$h=imagesy($image);$out=imagecreatetruecolor($w,$h);
  imagealphablending($out,FALSE);imagesavealpha($out,TRUE);
  for($y=0;$y<$h;$y++)for($x=0;$x<$w;$x++){
   $centre=imagecolorsforindex($image,imagecolorat($image,$x,$y));$channels=[[],[],[]];
   for($dy=-1;$dy<=1;$dy++)for($dx=-1;$dx<=1;$dx++){
    $c=imagecolorsforindex($image,imagecolorat($image,max(0,min($w-1,$x+$dx)),max(0,min($h-1,$y+$dy))));
    // An invisible RGB value must not bleed into visible pixels.
    if($c['alpha']===127)$c=$centre;
    $channels[0][]=$c['red'];$channels[1][]=$c['green'];$channels[2][]=$c['blue'];
   }
   foreach($channels as &$channel)sort($channel,SORT_NUMERIC);unset($channel);
   imagesetpixel($out,$x,$y,imagecolorallocatealpha($out,$channels[0][4],$channels[1][4],$channels[2][4],$centre['alpha']));
  }return $out;
 }];
