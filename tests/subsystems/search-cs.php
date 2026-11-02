<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function check($ok,$why='fixture'){if(!$ok)throw new RuntimeException($why);}
function padError($s){throw new InvalidArgumentException($s);}
function padMakeSafe($s,$n){return substr($s,0,$n);}
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
require PAD.'subs/search/_lib/czech.php';foreach(json_decode(file_get_contents(__DIR__.'/search-cs-vocabulary.json'),TRUE) as $word=>$want)check(padSearchCzechStem($word)===$want,$word);check(padSearchCzechStem('')==='');
