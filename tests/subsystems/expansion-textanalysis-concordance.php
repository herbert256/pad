<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
function padError($m){throw new InvalidArgumentException($m);}
require PAD.'lib/subsystem.php';
require PAD.'lib/textanalysis.php';
function check($v){if(!$v)throw new RuntimeException('fixture failed');}
function near($a,$b,$e=1e-10){check(abs($a-$b)<=$e*max(1,abs($b)));}
function rejects($f){try{$f();}catch(InvalidArgumentException $e){return;}throw new RuntimeException('invalid input accepted');}
$o=['normalization'=>'none','query'=>'STRASSE','context'=>2];$text="é Straße! Straße";$r=padTextAnalysis('concordance',$text,$o);check(count($r['matches'])===2);foreach($r['matches'] as $m)check(substr($text,$m['byteOffset'],$m['byteLength'])===$m['text']);check($r['matches'][0]['text']==='Straße');$r=padTextAnalysis('concordance',"e\u{0301} cat 👩‍💻",['normalization'=>'none','query'=>'cat','context'=>2]);check($r['matches'][0]['left']==="e\u{0301} ");$r=padTextAnalysis('concordance','ß ss',['normalization'=>'none','query'=>'s','mode'=>'substring']);check(count($r['matches'])===2);$r=padTextAnalysis('concordance','a a a',['normalization'=>'none','query'=>'a','limit'=>1]);check(count($r['matches'])===1&&$r['truncated']);rejects(fn()=>padTextAnalysis('concordance','text',['normalization'=>'none','query'=>'']));
echo "textanalysis:concordance fixtures passed\n";
