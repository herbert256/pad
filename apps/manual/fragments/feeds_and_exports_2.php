<?php

  $calendar = padCurl ( $padGoExt . 'fragments/feeds_and_exports_1&padFormat=ics' ) ['data'];
  $markdown = padCurl ( $padGoExt . 'fragments/feeds_and_exports_1&padFormat=md'  ) ['data'];

?>
