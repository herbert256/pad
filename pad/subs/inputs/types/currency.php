<?php

  // currency - A decimal amount with an explicit currency and a localized formatting preview.
  //

$currency = strtoupper((string)padInputTake('currency','EUR'));
if (!preg_match('/^[A-Z]{3}$/D',$currency)) { padError('currency needs a three-letter currency code'); return ''; }
$digits = padInputNumber('decimals',2,0,6,TRUE);
$value = padInputValue();
$locale = (string)padInputTake('locale','en');
return padInputField(function($head,$rest,$label,$error,$id,$worded) use($currency,$digits,$value,$locale){
 $own=padInputOwn($rest,['step'=>$digits ? '0.'.str_repeat('0',$digits-1).'1' : '1','inputmode'=>'decimal']);
 return padInputFrame('currency',$worded,$label.'<div class="pi-money"><span>'.padInputEscape($currency).'</span><input type="number" '.$head.$own.' value="'.padInputEscape($value).'"'.$rest.'></div><output class="pi-money-preview" aria-live="polite"></output>',$error,['currency'=>$currency,'decimals'=>$digits,'locale'=>$locale]);
});
?>
