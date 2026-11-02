<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function check($ok,$why='fixture'){if(!$ok)throw new RuntimeException($why);}
function padError($s){throw new InvalidArgumentException($s);}
function padMakeSafe($s,$n){return substr($s,0,$n);}
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
require PAD.'lib/form.php';$rule=include PAD.'subs/rules/types/required_if_any.php';foreach(json_decode(file_get_contents(__DIR__.'/rule-required_if_any.json'),TRUE) as [$v,$a,$d,$want])check($rule[is_array($v)?'list':'check']($v,$a,$d)===$want,json_encode([$v,$a,$d]));
