<?php

  // Implements the ignore option: wraps the level's text in {pad:ignore}...{/pad:ignore} so the braces
  // inside it are output literally instead of being parsed as tags. Start-phase option.

  $padContent = '{pad:ignore}' . $padContent . '{/pad:ignore}';

?>
