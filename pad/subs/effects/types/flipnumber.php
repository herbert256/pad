<?php
$value=padEffectText('value',padEffectValue());
if(!preg_match('/^-?\d{1,15}(?:\.\d{1,6})?$/D',$value)){padEffectBad('needs a finite decimal number, at most 15 integer and 6 decimal digits');return '';}
$html='<span class="pad-effect pad-effect-flipnumber" data-pad-flip="" data-value="'.padEffectEscape($value).'"><span class="pe-value">'.padEffectEscape($value).'</span><span class="pe-digits" aria-hidden="true"></span></span>';
return padEffectDone('flipnumber',padProtect($html)).padWidgetScript('flipnumber',padWidgetFile('effects/flipnumber.js','raw'));
?>
