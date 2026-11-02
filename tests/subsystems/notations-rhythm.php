<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
function check($v,$m='fixture'){if(!$v)throw new RuntimeException($m);}
require PAD.'subs/notations/_lib/rhythm.php';
$r=padRhythmRead("Kick: X... x... X... x...
Snare: .... x... .... x...");check(count($r['rows'])===2);$html=padRhythmHtml($r);check(substr_count($html,'aria-label="rest"')===26);check(substr_count($html,'scope="col"')===17);foreach([['Kick: xxxx','4/4',4],['Kick: xxxx','bad',1],['Kick: xxxx','4/4',9]] as $args){try{padRhythmRead(...$args);throw new RuntimeException('accepted bad rhythm');}catch(InvalidArgumentException $e){}}echo "rhythm meter, rests, accents and bounds OK
";
