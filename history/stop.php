<?php

  $padHstFile = "history/$app/$page/" . padFileTimestamp () . ".json";

  padFilePutContents ( $padHstFile, $padHstRsl );

?>