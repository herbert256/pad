<?php
$test=fn($value,$arg,$data)=>!padRuleSiblingIs($data,$arg)||!padRuleFilled($value);
return ['empty'=>TRUE,'check'=>$test,'list'=>$test,'argument'=>fn($arg)=>padRuleFieldValues($arg)[0]!==''&&padRuleFieldValues($arg)[1]?'':'use field,value', 'fields'=>fn($arg)=>[padRuleFieldValues($arg)[0]],'shown'=>fn($arg)=>padRuleShownIs($arg),'message'=>':label must be empty when :n'];
