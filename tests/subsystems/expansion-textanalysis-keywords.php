<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
function padError($m){throw new InvalidArgumentException($m);}
require PAD.'lib/subsystem.php';
require PAD.'lib/textanalysis.php';
function check($v){if(!$v)throw new RuntimeException('fixture failed');}
function near($a,$b,$e=1e-10){check(abs($a-$b)<=$e*max(1,abs($b)));}
function rejects($f){try{$f();}catch(InvalidArgumentException $e){return;}throw new RuntimeException('invalid input accepted');}
$o=['normalization'=>'none','method'=>'tfidf'];$r=padTextAnalysis('keywords',['a'=>'cat cat dog','b'=>'dog fish'],$o);$terms=array_column($r['documents'][0]['terms'],NULL,'term');near($terms['cat']['score'],2/3*(log(3/2)+1));near($terms['dog']['score'],1/3);check($terms['cat']['count']===2&&$terms['dog']['documents']===2);$r=padTextAnalysis('keywords',['cat CAT dog'],['normalization'=>'none','method'=>'frequency','stopwords'=>['DOG']]);check($r['terms'][0]['term']==='cat'&&$r['terms'][0]['count']===2&&$r['vocabulary']===1);rejects(fn()=>padTextAnalysis('keywords',['hello'],['normalization'=>'none','stopwords'=>['two words']]));
echo "textanalysis:keywords fixtures passed\n";
