<?php

  // Type handler for an effect: {effect:typewriter}Hello{/effect:typewriter}, or a bare tag
  // naming a file of subs/effects/types/ when nothing else has the name, {typewriter}Hello
  // {/typewriter}. The effect is the tag's name, and it runs as {effect 'typewriter'} runs it,
  // by subs/effects/effect.php - itself run as a built-in tag's PHP half is run,
  // types/_go/tag.php, on every walk the effect asks for.

  $padEffectKind = $padTag [$pad];

  $padTagGo = PAD . 'subs/effects/effect';

  return include PAD . 'types/_go/tag.php';

?>
