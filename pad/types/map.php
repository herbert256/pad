<?php

  // Map views, with choropleths, geographic markers and bubbles.

  $padMapView = $padTag [$pad] === 'auto' ? strtolower ( trim ( (string) $padParm ) ) : $padTag [$pad];
  $padTagGo = PAD . 'subs/maps/map';

  return include PAD . 'types/_go/tag.php';

?>
