<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function check($ok,$why='fixture'){if(!$ok)throw new RuntimeException($why);}
function padError($s){throw new InvalidArgumentException($s);}
function padMakeSafe($s,$n){return substr($s,0,$n);}
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
require PAD.'subs/tables/_lib/ranked.php';$rows=[['id'=>'b','score'=>9],['id'=>'a','score'=>9,'previous'=>3],['id'=>'c','score'=>5]];foreach(['dense'=>[1,1,2],'competition'=>[1,1,3],'ordinal'=>[1,2,3]] as $method=>$want){$r=padTableRanked($rows,'score',$method);check(array_column($r,'_rank')===$want);check($r[0]['id']==='a');check($r[0]['_change']===2);}check($rows[0]['id']==='b');
$large=padTableRanked([['id'=>'a','score'=>9007199254740992],['id'=>'b','score'=>9007199254740993]]);check(array_column($large,'_rank')===[1,2]);
