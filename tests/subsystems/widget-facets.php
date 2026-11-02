<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function check($ok,$why='fixture'){if(!$ok)throw new RuntimeException($why);}
function padError($s){throw new InvalidArgumentException($s);}
function padMakeSafe($s,$n){return substr($s,0,$n);}
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
require PAD.'subs/widgets/_lib/facets.php';$d=padFacetDecode(['x'=>['1','"1"','null','true']]);check($d===['x'=>[1,'1',NULL,TRUE]]);$h=padWidgetFacets(['facets'=>['x'=>[['value'=>1,'count'=>3]]]],['x'=>['1']]);check(substr_count($h,'type="checkbox"')===2);check(str_contains($h,'(0)'));try{padFacetDecode(['x'=>['{}']]);check(false);}catch(InvalidArgumentException $e){}
