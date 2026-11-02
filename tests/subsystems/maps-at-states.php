<?php
// Independent joins and geometry checks; run directly with PHP.
define('PAD',dirname ( __DIR__, 2 ).'/pad/');define('DATA','/nonexistent-pad-test-data/');
require PAD.'lib/dirs.php';require PAD.'lib/subsystem.php';require PAD.'lib/encoding.php';require PAD.'subs/maps/_lib/map.php';
function verify($ok,$why){if(!$ok)throw new RuntimeException($why);}
$view='at-states';$data=json_decode(file_get_contents(PAD.'subs/maps/_lib/'.$view.'.json'),TRUE,512,JSON_THROW_ON_ERROR);
verify(count($data['regions'])===9,'region count');verify(strlen(json_encode($data))<200000,'shape budget');
foreach($data['regions'] as $code=>$r){verify($r[4]!==''&&$r[5]!=='','both resolutions');verify(!preg_match('/(?:NAN|INF)/',$r[4]),'finite');verify($r[3][0]<$r[3][2]&&$r[3][1]<$r[3][3],'positive extent');foreach(array_merge([$code,$r[0],$r[1]],$r[2]) as $alias)verify(padMapCode($alias,$view)===$code,'join '.$alias);}
foreach($data['regions'] as $r){verify(!preg_match('/^M-?\d+,-?\d+l3,3-3,3-3-3z$/',$r[4]),'real outline, no placeholder');verify($r[3][0]>=0&&$r[3][1]>=0&&$r[3][2]<=$data['_box'][2]&&$r[3][3]<=$data['_box'][3],'geometry inside view');}
verify(padMapCode('unknown',$view)==='','unknown code');
echo "map at-states: OK\n";
$expected=explode(' ','1 2 3 4 5 6 7 8 9');$actual=array_map(fn($c)=>substr($c,3),array_keys($data['regions']));sort($expected);sort($actual);verify($actual===$expected,'independent identifiers');
verify($data['regions']['AT-8'][3][0]<$data['regions']['AT-9'][3][0],'west-east geography 8/9');
verify($data['regions']['AT-7'][3][0]<$data['regions']['AT-1'][3][0],'west-east geography 7/1');
