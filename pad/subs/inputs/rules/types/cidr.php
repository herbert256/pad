<?php
// IPv4/IPv6 prefix notation; host bits may be set. No zone IDs or padded prefix numbers.
return ['check'=>function($value){
 if(!is_string($value)||!preg_match('/^([^\/]+)\/(0|[1-9][0-9]{0,2})$/D',$value,$m))return FALSE;
 $ip=filter_var($m[1],FILTER_VALIDATE_IP);return $ip!==FALSE&&(int)$m[2]<=(str_contains($ip,':')?128:32);
},'message'=>':label must be an IPv4 or IPv6 CIDR prefix'];
?>
