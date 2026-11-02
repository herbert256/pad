<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}function padError($m){throw new InvalidArgumentException($m);}
require PAD.'lib/subsystem.php';require PAD.'lib/fake.php';
function check($v){if(!$v)throw new RuntimeException('fake fixture failed');}
padFakeSeed(117);$a=[padFake('hostname'),padFake('mime')];padFakeSeed(117);check($a===[padFake('hostname'),padFake('mime')]);
for($i=0;$i<500;$i++){check((bool)preg_match('/^host-[0-9]+\.example\.test$/D',padFake('hostname')));check(padFake('mime',['values'=>['application/json']])==='application/json');}
foreach([['hostname',['suffix'=>'-invalid.test']],['hostname',['suffix'=>str_repeat('a',64).'.test']],['mime',['values'=>[]]],['mime',['values'=>["text/plain\r\nInjected: x"]]]] as [$kind,$options]){try{padFake($kind,$options);}catch(InvalidArgumentException $e){continue;}throw new RuntimeException('invalid fake option accepted');}
echo "fake hostname/mime: deterministic seeds, bounded outputs and invalid options passed\n";
