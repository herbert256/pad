<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
function check($v,$m='fixture'){if(!$v)throw new RuntimeException($m);}
function padError($v){throw new RuntimeException($v);}require PAD.'subs/sounds/_lib/sound.php';require PAD.'subs/sounds/_lib/metronome.php';
$r=padMetronomeRecipe(120,4,2,1,.4);check($r['length']===4.0);$a=padSoundRender($r);check(count($a)===88200);check($a===padSoundRender($r));check(max(array_map('abs',$a))<=13107);check($r['voices'][4]['start']===2.0);check(padMetronomeRecipe(120,4,1,1,0)['level']===0.0);try{padMetronomeRecipe(30,12,4);throw new RuntimeException('unbounded duration');}catch(InvalidArgumentException $e){}echo "metronome duration, beat positions, deterministic PCM and peaks OK
";
