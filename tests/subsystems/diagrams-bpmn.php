<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
function check($v,$m='fixture'){if(!$v)throw new RuntimeException($m);}
require PAD.'subs/diagrams/_lib/bpmn.php';
$text=<<<'SOURCE'
lane sales "Sales"
lane ops "Operations"
start begin sales "Order received"
task check sales "Check order"
exclusive paid sales "Paid?"
task pack ops "Pack goods"
end done ops "Dispatched"
begin -> check
check -> paid
paid -> pack "yes"
pack -> done
SOURCE;
$d=padBpmnRead($text);check(count($d['nodes'])===5);
try{padBpmnRead('lane a "A"
subprocess x a "No"');throw new RuntimeException('accepted unsupported/cyclic syntax');}catch(InvalidArgumentException $e){}
echo "bpmn grammar and invalid graph fixtures OK\n";
