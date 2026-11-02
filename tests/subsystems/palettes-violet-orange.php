<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
function check($v,$m='fixture'){if(!$v)throw new RuntimeException($m);}
require PAD.'subs/palettes/_lib/palette.php';
$kind='violet-orange';$type='diverging';
check(padPaletteData($kind)['type']===$type);check(count(padPaletteSelect($kind,$type,256))===256);foreach(['light','dark'] as $scheme)check(isset(padPaletteMetadata($kind)[$scheme]['outlineRecommended']));check(count(padPaletteUsage($kind,'categorical',4))>0);$v=array_map('padPaletteLuminance',padPaletteColors($kind, 9));
check(padPaletteColors($kind,1)[0]==='#faf7f1');for($i=1;$i<=4;$i++)check($v[$i]>$v[$i-1]);for($i=5;$i<9;$i++)check($v[$i]<$v[$i-1]);
echo "$kind: metadata, luminance, selection OK\n";
