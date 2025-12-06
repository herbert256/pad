<?php

  foreach ( glob ( PAD . 'eval/functions/*.php' ) as $padIncludeFile )
    include_once  $padIncludeFile;

?>