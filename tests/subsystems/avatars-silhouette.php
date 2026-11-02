<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
function check($v,$m='fixture'){if(!$v)throw new RuntimeException($m);}
require PAD.'subs/avatars/_lib/silhouette.php';
foreach(['person','organisation'] as $kind)foreach([8,16,24,64] as $size){$svg=padAvatarSilhouette($kind,$size,'Unknown <user>');$xml=new DOMDocument();check($xml->loadXML($svg));check(str_contains($svg,'Unknown &lt;user&gt;'));check(str_contains($svg,'currentColor'));}try{padAvatarSilhouette('inferred');throw new RuntimeException('accepted bad variant');}catch(InvalidArgumentException $e){}echo "silhouette variants, small sizes, inherited theme ink OK
";
