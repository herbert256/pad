<?php
// General Luhn checksum, 2 to 32 digits. Spaces/hyphens are allowed separators, no card semantics.
return ['check'=>function($value){
 if(!is_string($value)||strlen($value)>64||!preg_match('/^[0-9][0-9 -]*[0-9]$/D',$value))return FALSE;
 $digits=str_replace([' ','-'],'',$value);if(strlen($digits)<2||strlen($digits)>32)return FALSE;
 $sum=0;$double=FALSE;for($i=strlen($digits)-1;$i>=0;$i--){$n=(int)$digits[$i];if($double){$n*=2;if($n>9)$n-=9;}$sum+=$n;$double=!$double;}return $sum%10===0;
},'message'=>':label must have a valid Luhn checksum'];
?>
