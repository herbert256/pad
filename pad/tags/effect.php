<?php

  // {effect 'typewriter'}Hello, world{/effect} or {effect 'countup', to=1250/} - motion or
  // decoration on a text or a block, of the kind the first parameter names: a kind is a file
  // of subs/effects/types/, run by the effects subsystem in subs/effects/ - the same effect as
  // the effect: type writes, {effect:typewriter}...{/effect:typewriter}, and the bare name,
  // {typewriter}...{/typewriter}. The effect's own first parameter - the text of {typewriter
  // 'Hello'/} - is the second here, and is handed on as the first, on every walk.

  $padEffectKind = is_string ( $padParm ) ? strtolower ( trim ( $padParm ) ) : '';
  $padParm       = $padOpt [$pad] [2] ?? '';

  return include PAD . 'subs/effects/effect.php';

?>
