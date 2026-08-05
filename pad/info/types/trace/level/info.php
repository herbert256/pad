<?php

  // Traces what a level holds once it has been set up, by running the six reporting events in
  // turn: its data, its flags, its content, the true and false branches, and the base text.
  //
  // Included from events/build.php and events/levelStart.php. Each event file guards itself with
  // its own $padInfoTrace* switch, so which of the six actually say anything depends on the
  // config/info/<mode>.php in force.

  include PAD . 'events/data.php';
  include PAD . 'events/flags.php';
  include PAD . 'events/content.php';
  include PAD . 'events/true.php';
  include PAD . 'events/false.php';
  include PAD . 'events/base.php';

?>