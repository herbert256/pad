<?php
$test=fn($value,$arg,$data)=>padRuleFilled($value)||!array_filter(explode(';',$arg),fn($part)=>padRuleSiblingIs($data,$part));
return ['empty'=>TRUE,'check'=>$test,'list'=>$test,
 'argument'=>fn($arg)=>$arg!==''&&count(explode(';',$arg))<=20&&!array_filter(explode(';',$arg),fn($p)=>padRuleFieldValues($p)[0]===''||!padRuleFieldValues($p)[1])?'':'use field,value;other,value, at most 20 conditions',
 'fields'=>fn($arg)=>array_map(fn($p)=>padRuleFieldValues($p)[0],explode(';',$arg)),
 'shown'=>fn($arg)=>implode(' or ',array_map('padRuleShownIs',explode(';',$arg))), 'message'=>':label is required when :n'];
