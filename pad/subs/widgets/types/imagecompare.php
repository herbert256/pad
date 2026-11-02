<?php

  // {widget:imagecompare before='before.svg', after='after.svg'/} - Two images separated by an adjustable divider with a native range control.
  //

$before=padWidgetLink(padWidgetText('before'));$after=padWidgetLink(padWidgetText('after'));
if($before===''||$after===''){padError('imagecompare needs before and after image URLs');return '';}
$a=padWidgetAttr(padWidgetText('beforelabel','Before'));$b=padWidgetAttr(padWidgetText('afterlabel','After'));
return padWidgetSheet(padWidgetFile('widgets/imagecompare.css','raw')).'<figure class="pad-imagecompare"><div class="pic-images"><figure><img src="'.$before.'" alt="'.$a.'"><figcaption>'.$a.'</figcaption></figure><figure><img src="'.$after.'" alt="'.$b.'"><figcaption>'.$b.'</figcaption></figure></div><label hidden>Comparison divider <input type="range" min="0" max="100" value="50" aria-label="Comparison divider"></label></figure>'.padWidgetScript('imagecompare',padWidgetFile('widgets/imagecompare.js','raw'));
?>
