<?php
 define('PAD',dirname(__DIR__,2).'/pad/');require PAD.'subs/tables/_lib/structured.php';function ck($ok,$why){if(!$ok)throw new Exception($why);}
 $roots=padTableTree([['id'=>'r','name'=>'Root'],['id'=>'b','parent'=>'r','name'=>'B'],['id'=>'a','parent'=>'r','name'=>'A'],['id'=>'r2','name'=>'Other']],'id','parent','name');ck(count($roots)===2,'root branches');ck(array_column(array_column($roots[1],'row'),'id')===['r','a','b'],'sibling sort preserves root');ck(array_column($roots[1],'depth')===[0,1,1],'depth');
 foreach([[['id'=>'a'],['id'=>'a']],[['id'=>'a','parent'=>'b']],[['id'=>'a','parent'=>'a']],[['id'=>'a','parent'=>'b'],['id'=>'b','parent'=>'a']]] as $rows){try{padTableTree($rows);throw new Exception('Expected rejection');}catch(InvalidArgumentException $e){}}
 $chain=[['id'=>'0']];for($i=1;$i<67;$i++)$chain[]=['id'=>(string)$i,'parent'=>(string)($i-1)];try{padTableTree($chain);throw new Exception('Expected depth rejection');}catch(LengthException $e){}
 echo "Application tables: root branches, sibling sort, duplicate/orphan/cycle rejection passed\n";
