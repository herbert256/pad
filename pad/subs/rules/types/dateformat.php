<?php
return ['argument'=>fn($arg)=>in_array($arg,['Y-m-d','d/m/Y','m/d/Y','Y-m-dTH:i'],TRUE)?'':'use Y-m-d, d/m/Y, m/d/Y or Y-m-dTH:i',
 'check'=>function($value,$arg){$formats=['Y-m-d'=>'Y-m-d','d/m/Y'=>'d/m/Y','m/d/Y'=>'m/d/Y','Y-m-dTH:i'=>'Y-m-d\TH:i'];if(!isset($formats[$arg])||!is_string($value))return FALSE;$d=DateTimeImmutable::createFromFormat('!'.$formats[$arg],$value,new DateTimeZone('UTC'));return $d&&$d->format($formats[$arg])===$value&&$d->format('Y')!=='0000';},
 'message'=>':label must match date format :n'];
