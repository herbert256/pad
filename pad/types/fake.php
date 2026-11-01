<?php

  // Type handler for a fake kind: {fake:email/}, {fake:number from=1, to=6/}. The kind is
  // the tag's name, and the value is made as {fake 'email'} makes it, by subs/fakes/fake.php -
  // run as a built-in tag's PHP half is run, types/_go/tag.php. A fake kind is never reached
  // bare: its names - email, city, date - are the names of fields and pages.

  $padFakeSpec = $padTag [$pad];

  $padTagGo = PAD . 'subs/fakes/fake';

  return include PAD . 'types/_go/tag.php';

?>
