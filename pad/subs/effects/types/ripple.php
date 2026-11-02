<?php
$content=padEffectContent();
if($content===NULL){padEffectBad('needs an existing control between its tags');return '';}
return padEffectDone('ripple',padProtect('<span class="pad-effect pad-effect-ripple">').$content.padProtect('</span>')).padWidgetScript('ripple',padWidgetFile('effects/ripple.js','raw'));
?>
