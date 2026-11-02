<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
function check($v,$m='fixture'){if(!$v)throw new RuntimeException($m);}
require PAD.'lib/encoding.php';require PAD.'lib/fake.php';require PAD.'subs/patterns/_lib/pattern.php';
$s=include PAD.'subs/patterns/types/contourdots.php';
$p=['w'=>240,'h'=>160,'size'=>80,'id'=>'fixture','bg'=>'#fff','ink'=>['#123','#456']];foreach($s['options'] as $k=>$v)$p[$k]=$v[0];padFakeSeed(17);$a=$s['draw']($p);padFakeSeed(17);check($a===$s['draw']($p),'repeatability');$xml=new DOMDocument();check($xml->loadXML('<svg xmlns="http://www.w3.org/2000/svg">'.implode('',$a).'</svg>'));check($s['count'][2]($p)<=$s['count'][1]);check(!preg_match('/NAN|INF/',implode('',$a)));echo "pattern seeded geometry and budget OK
";
