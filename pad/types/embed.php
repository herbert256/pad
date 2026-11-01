<?php

  // Type handler for an embed provider: {embed:youtube 'aqz-KE-bpKQ', title='Big Buck Bunny'/},
  // or a bare tag naming a file of subs/embeds/types/ when nothing else has the name,
  // {openstreetmap '52.3731,4.8922', title='Dam Square'/}. The provider is the tag's name,
  // and the embed is written as {embed} writes it, by subs/embeds/embed.php - run as a built-in
  // tag's PHP half is run, types/_go/tag.php.

  $padEmbedName = $padTag [$pad];

  $padTagGo = PAD . 'subs/embeds/embed';

  return include PAD . 'types/_go/tag.php';

?>
