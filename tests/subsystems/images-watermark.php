<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
function check($v,$m='fixture'){if(!$v)throw new RuntimeException($m);}
require PAD.'subs/images/_lib/watermark.php';
function canvas($p){$i=imagecreatetruecolor(40,40);imagealphablending($i,FALSE);imagesavealpha($i,TRUE);imagefill($i,0,0,$p);return $i;}$mark=canvas(0xff0000);$im=padWatermarkCompose(canvas(0x7f000000),$mark,.5,'center',.5,0);$p=imagecolorat($im,20,20);check(($p&0xffffff)===0xff0000);check((($p>>24)&127)===64);check(imagecolorat($im,0,0)===0x7f000000);$none=padWatermarkCompose(canvas(0x123456),$mark,0,'top-left',.5,0);check(imagecolorat($none,0,0)===0x123456);echo "watermark alpha composition, position, opacity zero OK
";
