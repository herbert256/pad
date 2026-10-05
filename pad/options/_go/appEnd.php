<?php

  // Runs the application's end phase of the option walk: the options of this tag for which
  // the application supplies a handler in an _options/end/ directory, collected into
  // $padOptionsAppEnd [$pad] while the parameters were parsed. Included by level/end.php as
  // the level closes, just before the built-in end phase, and like it works on the rendered
  // $padResult [$pad] - where an _options/ handler only ever saw the template, so an option
  // could not touch what the tag produced: {report minify}, {price highlight}.

  $padOptions = 'appEnd';

  include PAD . 'options/_go/options.php';

?>