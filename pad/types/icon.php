<?php

  // Type handler for an icon: {icon:star size=20/}, the icon: prefix naming a file of
  // subs/icons/types/ - never a bare name, the icons' names being those of fields and pages. The
  // icon is the tag's name, and it is drawn as {icon 'star', size=20/} draws it, by
  // subs/icons/icon.php - run as a built-in tag's PHP half is run, types/_go/tag.php.

  $padIconName = $padTag [$pad];

  $padTagGo = PAD . 'subs/icons/icon';

  return include PAD . 'types/_go/tag.php';

?>
