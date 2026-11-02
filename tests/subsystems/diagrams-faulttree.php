<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
function check($v,$m='fixture'){if(!$v)throw new RuntimeException($m);}
require PAD.'subs/diagrams/_lib/faulttree.php';
$text=<<<'SOURCE'
top failure "Service unavailable"
or gate "Failure causes"
event power "Power lost"
event network "Network lost"
failure -> gate
gate -> power
gate -> network
SOURCE;
$d=padFaulttreeRead($text);check(count($d['nodes'])===4);
try{padFaulttreeRead('top root "R"
event a "A"
root -> a
a -> root');throw new RuntimeException('accepted unsupported/cyclic syntax');}catch(InvalidArgumentException $e){}
echo "faulttree grammar and invalid graph fixtures OK\n";
