<?php

  // reorder - Wrap up to 500 direct children with unique nonempty data-pad-key attributes. Call `padReorder(container, completeKeyPermutation)` after deciding a new order; invalid keys are rejected before mutation. DOM order updates synchronously and focused descendants retain focus. `duration=0..1000` milliseconds, default 200. Visual displacement is bounded to 2000 px; reduced motion skips animation. Removed elements need no persistent listeners or observers. No JavaScript leaves the original accessible order.
  //
  // ## Example
  //
  // ```pad
  // {effect:reorder}<p data-pad-key='a'>First</p><p data-pad-key='b'>Second</p>{/effect:reorder}
  // ```
  //
  // Run the focused fixture with `node tests/subsystems/effect-reorder.cjs`.

$padFxContent=padEffectContent();$padFxDuration=padEffectInt('duration',200,0,1000);
if($padFxContent===NULL){padEffectBad('reorder wraps children with unique data-pad-key attributes');return '';}
return padEffectDone('reorder',padProtect('<div class="pad-effect-reorder" data-pad-reorder="'.$padFxDuration.'">').$padFxContent.padProtect('</div>').padProtect(padWidgetScript('effect-reorder',padEffectAsset('reorder.js'))));
