<?php
$padFxContent=padEffectContent();$padFxDuration=padEffectInt('duration',200,0,1000);
if($padFxContent===NULL){padEffectBad('reorder wraps children with unique data-pad-key attributes');return '';}
return padEffectDone('reorder',padProtect('<div class="pad-effect-reorder" data-pad-reorder="'.$padFxDuration.'">').$padFxContent.padProtect('</div>').padProtect(padWidgetScript('effect-reorder',padEffectAsset('reorder.js'))));
