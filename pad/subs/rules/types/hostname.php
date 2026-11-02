<?php
// ASCII DNS hostname (punycode A-labels accepted), optional trailing root dot, no IP literals.
return ['check'=>function($value){
 if(!is_string($value)||strlen($value)>254)return FALSE;$host=rtrim($value,'.');
 if($host===''||strlen($host)>253||filter_var($host,FILTER_VALIDATE_IP)!==FALSE||str_ends_with($value,'..'))return FALSE;
 foreach(explode('.',$host) as $label)if(!preg_match('/^[A-Za-z0-9](?:[A-Za-z0-9-]{0,61}[A-Za-z0-9])?$/D',$label))return FALSE;
 return TRUE;
},'message'=>':label must be an ASCII DNS hostname'];
?>
