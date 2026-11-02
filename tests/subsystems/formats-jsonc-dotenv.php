<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);} function padError($m){throw new InvalidArgumentException($m);} function padMakeSafe($s,$n){return substr($s,0,$n);}
$padCheckSyntax=TRUE;require PAD.'lib/subsystem.php';require PAD.'lib/format.php';
function check($value,$why){if(!$value)throw new RuntimeException($why);}
function rejects($call){try{$call();}catch(InvalidArgumentException $e){return;}throw new RuntimeException('invalid data accepted');}
$text="{/* hi */\n\"url\":\"https://example.test/a/*b*/\",// line\n\"list\":[1,2,],}";
check(padFormatRead('jsonc',$text)===['url'=>'https://example.test/a/*b*/','list'=>[1,2]],'comments/strings/trailing');
check(padFormatRead('jsonc','{"a":1,"a":2}')===['a'=>2],'duplicate JSON last value');
foreach(['[,]', '{"a":,}', '[1,,]', '{/*', 'NaN', "[\"a\n\"]", "[1\0]"] as $bad)rejects(fn()=>padFormatRead('jsonc',$bad));
rejects(fn()=>padFormatRead('jsonc','[1,]',['trailing'=>FALSE]));
rejects(fn()=>padFormatRead('jsonc','[]',['wrong'=>TRUE]));
$values=['UNICODE'=>'é😀','LITERAL'=>'${HOME} $(touch /tmp/never) `id`','NEWLINES'=>"a\nb\t","EMPTY"=>'','QUOTE'=>'"quoted"'];
check(padFormatRead('dotenv',padFormatWrite('dotenv',$values))===$values,'literal roundtrip');
check(padFormatRead('dotenv',"export A=' a # b '\nB=bare # tail\nC=hash#data\nD=\"line\\nnext\" # tail")===['A'=>' a # b ','B'=>'bare','C'=>'hash#data','D'=>"line\nnext"],'quote and comment contract');
foreach(["A=1\nA=2",'1KEY=value','A="unterminated','A="\\q"',"A=\"\\u0000\""] as $bad)rejects(fn()=>padFormatRead('dotenv',$bad));
foreach(["KEY=abc\0","\0KEY=abc","KEY=\0"] as $bad)rejects(fn()=>padFormatRead('dotenv',$bad));
check(padFormatRead('dotenv',"A=1\nA=2",['duplicates'=>'last'])===['A'=>'2'],'explicit duplicate last');
echo "JSONC/dotenv: comments, escapes, literal values, roundtrips and malformed inputs passed\n";
