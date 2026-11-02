<?php
  require __DIR__ . '/_expansion_boot.php'; require PAD . 'lib/colors.php';
  subNear(padColor('contrast','#000','#fff')['ratio'],21);
  subNear(padColor('contrast','#777','#fff')['ratio'],4.478089453577214);
  subCheck(padColor('mix','#f00','#00f')['hex']==='#800080');
  subCheck(padColor('mix','#f00','#00f',['space'=>'linear-srgb'])['hex']==='#bc00bc');
  subCheck(padColor('mix','#f000','#00ff')['hex']==='#0000ff80','premultiplied alpha');
  subCheck(padColor('mix','#f000','#00f0')['hex']==='#00000000');
  subCheck(padColor('convert','#000',NULL,['to'=>'hsl'])['channels']===[0.0,0.0,0],'achromatic');
  $hsl=padColor('convert','#336699',NULL,['to'=>'hsl']);subNear($hsl['channels'][0],210);subNear($hsl['channels'][1],.5);subNear($hsl['channels'][2],.4);
  subCheck(padColor('convert',['space'=>'hsl','channels'=>$hsl['channels']])['hex']==='#336699','HSL round trip');
  foreach([[1,1,0.9999999999999999],[0,0,5e-324]] as $edge){$answer=padColor('convert',['channels'=>$edge],NULL,['to'=>'hsl']);foreach($answer['channels'] as $channel)subCheck(is_finite((float)$channel),'HSL edge remains finite');subCheck($answer['channels'][1]>=0&&$answer['channels'][1]<=1,'HSL edge saturation');}
  subNear(padColor('contrast','#0000','#fff')['ratio'],1);
  subReject(fn()=>padColor('contrast','#fff','#0008'));
  subNear(padColor('contrast','#fff','#0000',['background'=>'#fff'])['ratio'],1);
  subCheck(!padColor('gamut',['channels'=>[1.1,0,-.1]])['inGamut']);
  subCheck(padColor('gamut',['channels'=>[1.1,0,-.1]],NULL,['method'=>'clip'])['color']['hex']==='#ff0000');
  require PAD.'subs/palettes/_lib/palette.php'; require PAD.'subs/avatars/_lib/avatar.php';
  $old=function($hex,$threshold){$out=0;foreach([.2126,.7152,.0722] as $i=>$w){$c=hexdec(substr($hex,1+2*$i,2))/255;$out+=$w*($c<=$threshold?$c/12.92:(($c+.055)/1.055)**2.4);}return $out;};
  $hexes=[];for($n=0;$n<256;$n++)$hexes[]=sprintf('#%02x%02x%02x',$n,$n,$n);
  for($r=0;$r<256;$r+=17)for($g=0;$g<256;$g+=17)for($b=0;$b<256;$b+=17)$hexes[]=sprintf('#%02x%02x%02x',$r,$g,$b);
  foreach($hexes as $hex){subCheck(padPaletteLuminance($hex)===$old($hex,.04045),'palette compatibility');subCheck(padAvatarLuminance($hex)===$old($hex,.03928),'avatar compatibility');$linear=padColor('convert',$hex,NULL,['to'=>'linear-srgb']);subCheck(padColor('convert',['space'=>'linear-srgb','channels'=>$linear['channels']])['hex']===$hex,'transfer round trip');}
  foreach([fn()=>padColor('convert','#gggggg'),fn()=>padColor('convert',['channels'=>[NAN,0,0]]),fn()=>padColor('convert',['channels'=>[1.1,0,0]]),fn()=>padColor('convert',['channels'=>[0,0,0],'alpha'=>NULL]),fn()=>padColor('mix','#000','#fff',['amount'=>2]),fn()=>padColor('gamut','#fff',NULL,['method'=>'magic'])] as $bad)subReject($bad);
  echo "colors: reference vectors, alpha, gamut, 4352 legacy/transfer comparisons passed\n";
?>
