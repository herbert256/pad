<?php

  // Type handler for a section: {section:hero title='...'} ... {/section:hero}, the section
  // the tag's name names - a file of subs/sections/types/. Never reached bare: hero, team,
  // stats and footer are the names of fields and pages, so the section: prefix is always
  // written (lib/subsystem.php). It runs as {section 'hero'} runs it, by
  // subs/sections/section.php - itself run as a built-in tag's PHP half is run,
  // types/_go/tag.php, on every walk the section asks for.

  $padSectionKind = $padTag [$pad];

  $padTagGo = PAD . 'subs/sections/section';

  return include PAD . 'types/_go/tag.php';

?>
