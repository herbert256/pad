<?php
// Independent joins and geometry checks; run directly with PHP.
define('PAD',dirname(__DIR__,3).'/pad/');define('DATA','/nonexistent-pad-test-data/');
require PAD.'lib/dirs.php';require PAD.'lib/subsystem.php';require PAD.'lib/encoding.php';require PAD.'subs/maps/_lib/map.php';
function verify($ok,$why){if(!$ok)throw new RuntimeException($why);}
$view='ca-provinces';$data=json_decode(file_get_contents(PAD.'subs/maps/_lib/'.$view.'.json'),TRUE,512,JSON_THROW_ON_ERROR);
verify(count($data['regions'])===13,'region count');verify(strlen(json_encode($data))<200000,'shape budget');
foreach($data['regions'] as $code=>$r){verify($r[4]!==''&&$r[5]!=='','both resolutions');verify(!preg_match('/(?:NAN|INF)/',$r[4]),'finite');verify($r[3][0]<$r[3][2]&&$r[3][1]<$r[3][3],'positive extent');foreach(array_merge([$code,$r[0],$r[1]],$r[2]) as $alias)verify(padMapCode($alias,$view)===$code,'join '.$alias);}
verify(padMapCode('unknown',$view)==='','unknown code');
echo "map ca-provinces: OK\n";
