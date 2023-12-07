<?php

                  include pad . 'config/config.php';
                  include pad . "config/output/$padOutputType.php";
  if ( $padInfo ) include pad . "config/info/$padInfo.php";

  if ( file_exists ( padApp . '_config/config.php' ) ) 
    include padApp . '_config/config.php';

  if ( isset ( $padSetConfig ) and count ( $padSetConfig ) ) 
    include_once pad . "inits/configSet.php";

  if ( file_exists ( "output/lib/$padOutputType.php" ) )
    include_once pad . "output/lib/$padOutputType.php";

?>