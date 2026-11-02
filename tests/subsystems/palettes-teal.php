<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
function check($v,$m='fixture'){if(!$v)throw new RuntimeException($m);}
require PAD.'subs/palettes/_lib/palette.php';
$kind='teal';$type='sequential';
check(padPaletteData($kind)['type']===$type);check(count(padPaletteSelect($kind,$type,256))===256);foreach(['light','dark'] as $scheme)check(isset(padPaletteMetadata($kind)[$scheme]['outlineRecommended']));check(count(padPaletteUsage($kind,'categorical',4))>0);$v=array_map('padPaletteLuminance',padPaletteColors($kind, 9));
for($i=1;$i<count($v);$i++)check($v[$i]<$v[$i-1]);
echo "$kind: metadata, luminance, selection OK\n";
