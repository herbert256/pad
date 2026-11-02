<?php
 define('PAD',dirname(__DIR__,2).'/pad/');$cases=[
 'hostname'=>[['example.com',TRUE],['localhost',TRUE],['xn--bcher-kva.example.',TRUE],['bücher.example',FALSE],['-bad.example',FALSE],['a..b',FALSE],['a..',FALSE],['127.0.0.1',FALSE],['::1',FALSE],[str_repeat('a',64).'.com',FALSE]],
 'cidr'=>[['192.0.2.1/24',TRUE],['0.0.0.0/0',TRUE],['::/0',TRUE],['2001:db8::1/128',TRUE],['1.2.3.4/33',FALSE],['::1/129',FALSE],['::1/01',FALSE],['fe80::1%eth0/64',FALSE],['256.1.1.1/24',FALSE]],
 'luhn'=>[['79927398713',TRUE],['7992-7398 713',TRUE],['79927398714',FALSE],['0',FALSE],['00',TRUE],['12x',FALSE],[str_repeat('0',33),FALSE]]];
 foreach($cases as $kind=>$values){$rule=include PAD."subs/inputs/rules/types/$kind.php";foreach($values as [$value,$expected])if($rule['check']($value)!==$expected)throw new Exception("$kind: $value");}
 if(in_array('--json',$argv,TRUE))echo json_encode($cases);else echo "Application rules: hostname, CIDR and Luhn server fixtures passed\n";
