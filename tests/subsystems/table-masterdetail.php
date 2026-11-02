<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function check($ok,$why='fixture'){if(!$ok)throw new RuntimeException($why);}
function padError($s){throw new InvalidArgumentException($s);}
function padMakeSafe($s,$n){return substr($s,0,$n);}
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
require PAD.'subs/tables/_lib/masterdetail.php';$r=[['id'=>'a','name'=>'Beta','detail'=>'<x>'],['id'=>'b','name'=>'Alpha','detail'=>'safe']];$h=padTableMasterDetail($r,['id','name'],['sort'=>'name','pagesize'=>1]);check(str_contains($h,'Details for b'));check(!str_contains($h,'Details for a'));$h=padTableMasterDetail($r,['id'],['template'=>'<p>[[detail]]</p>']);check(str_contains($h,'<p>&lt;x&gt;</p>'));try{padTableMasterDetail([$r[0],$r[0]],['id']);check(false);}catch(InvalidArgumentException $e){}
