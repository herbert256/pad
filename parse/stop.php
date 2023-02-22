<?php

  $padParseFile = "parse/$app/$page/" . padFileTimestamp () . ".json";

  padFilePutContents ( $padParseFile, $padParseResult );

  echo "<pre>" . htmlentities(padJson ($padParseResult)) . "</pre>";

  padExit();

?>