<?php

  foreach ( glob ( 'eval/functions/*.php' ) as $padIncludeFile )
    include_once  $padIncludeFile;

?>