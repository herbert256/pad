<?php
function padFormFind($data,$name){return [isset($data[$name]),$data[$name]??NULL];}
function padValidate($rules,$data){return $rules;}
require __DIR__.'/../../pad/subs/inputs/_lib/repeatable.php';
$r=padValidateRows('lines',['quantity'=>'numeric'],['lines'=>[2=>['quantity'=>'bad'],5=>['quantity'=>1]]]);
if(array_keys($r)!==['lines[2][quantity]','lines[5][quantity]'])throw new Exception('Nested addresses lost');
if(!isset(padValidateRows('lines',[],['lines'=>'bad'])['lines']))throw new Exception('Malformed rows accepted');
echo "repeatable: nested addresses and malformed rows passed\n";
