<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function check($ok,$why='fixture'){if(!$ok)throw new RuntimeException($why);}
function padError($s){throw new InvalidArgumentException($s);}
function padMakeSafe($s,$n){return substr($s,0,$n);}
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
require PAD.'lib/fake.php'; padFakeSeed(7); $a=padFake('locale',['supported'=>['cs-CZ','en']]); padFakeSeed(7); check($a===padFake('locale',['supported'=>['cs-CZ','en']])); check(in_array($a,['cs-CZ','en'])); try {padFake('locale',['supported'=>[]]);check(false);}catch(InvalidArgumentException $e){}
