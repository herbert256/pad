<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
function check($v,$m='fixture'){if(!$v)throw new RuntimeException($m);}
require PAD.'subs/filters/_lib/filter.php';
$s=padFilterKind('saturation');
function pixel($v){$i=imagecreatetruecolor(1,1);imagealphablending($i,FALSE);imagesavealpha($i,TRUE);imagesetpixel($i,0,0,$v);return $i;}
foreach([1,2] as $scale)foreach([0x00808080,0x3f808080,0x7f808080] as $p)check(imagecolorat($s['apply'](pixel($p),['amount'=>1],$scale),0,0)===$p,'identity alpha');
check((imagecolorat($s['apply'](pixel(0x3f808080),['amount'=>0],1),0,0)&0xffffff)===0x808080);
check((imagecolorat($s['apply'](pixel(0xff0000),['amount'=>0],1),0,0)&0xffffff)===0x363636,'independent red luma');
echo "filter saturation: reference pixels and alpha OK\n";
