<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function check($ok,$why='fixture'){if(!$ok)throw new RuntimeException($why);}
function padError($s){throw new InvalidArgumentException($s);}
function padMakeSafe($s,$n){return substr($s,0,$n);}
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
require PAD.'subs/inputs/_lib/transferlist.php';$c=['a'=>'A','b'=>'B'];check(padInputTransferList(['b','a'],$c)===['a','b']);check(padInputTransferList(['a','b'],$c,TRUE,['a'=>2,'b'=>1])===['b','a']);foreach([['x'],['a','a']] as $ids){try{padInputTransferList($ids,$c);check(false);}catch(InvalidArgumentException $e){}}
