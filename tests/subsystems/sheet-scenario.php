<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function check($ok,$why='fixture'){if(!$ok)throw new RuntimeException($why);}
function padError($s){throw new InvalidArgumentException($s);}
function padMakeSafe($s,$n){return substr($s,0,$n);}
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
require PAD.'subs/sheets/_lib/scenario.php';$m=['A1'=>2,'B1'=>'=A1*3'];$a=padSheetScenario($m,['Low'=>['A1'=>1],'High'=>['A1'=>5]],['A1'],['B1']);check($a[0]['outputs']['B1']===3);check($a[1]['outputs']['B1']===15);check($m['A1']===2);try{padSheetScenario($m,['bad'=>['A1'=>'=1']],['A1'],['B1']);check(false);}catch(InvalidArgumentException $e){}
