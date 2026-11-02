<?php
include_once PAD.'lib/formulas.php';
function padSheetScenario($model,$scenarios,$inputs,$outputs,$seed=0){
 if(!is_array($model)||!$model||!is_array($scenarios)||count($scenarios)>50||!is_array($inputs)||!is_array($outputs)||!$outputs||count($outputs)>100||count($model)*max(1,count($scenarios))>20000)throw new InvalidArgumentException('scenario limits: 50 scenarios, 100 outputs, 20000 scenario-cell work');
 $valid=fn($s)=>is_string($s)&&preg_match('/^[A-Z]{1,3}[1-9][0-9]{0,3}$/D',$s);
 foreach(array_merge(array_keys($model),$inputs,$outputs) as $address)if(!$valid($address))throw new InvalidArgumentException('scenario needs uppercase A1 addresses');
 foreach($inputs as $address)if(!array_key_exists($address,$model))throw new InvalidArgumentException('scenario input missing in model');
 $rows=[];foreach($scenarios as $name=>$overrides){if(!is_string($name)||trim($name)===''||!is_array($overrides))throw new InvalidArgumentException('scenario requires named override maps');$copy=$model;$given=[];
 foreach($overrides as $address=>$value){if(!in_array($address,$inputs,TRUE)||(!is_scalar($value)&&$value!==NULL)||(is_float($value)&&!is_finite($value))||(is_string($value)&&(strlen($value)>1000||str_starts_with(ltrim($value),'='))))throw new InvalidArgumentException('scenario override must be a declared input scalar, not a formula');$copy[$address]=$value;}
 foreach($inputs as $address)$given[$address]=$copy[$address];$computed=padSheet($copy,$seed);$result=[];foreach($outputs as $address)$result[$address]=$computed[$address]??NULL;$rows[]=['name'=>$name,'inputs'=>$given,'outputs'=>$result];}
 return $rows;
}
