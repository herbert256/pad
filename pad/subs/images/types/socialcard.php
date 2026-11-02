<?php
// socialcard - local, reproducible PNG with packaged Noto Sans and explicit text limits.
$options=[];foreach(['title'=>'','description'=>'','brand'=>'','logo'=>'','alt'=>'','font'=>'noto-sans','width'=>1200,'height'=>630] as $key=>$default)$options[$key]=padTagParm($key,$default);
return padImageSocialCard($options);
