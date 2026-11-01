<?php

  // The rules on a duration picker of the template are those of the seconds it posts, checked
  // before the page's PHP as those of {input}: a post of text that is no number of seconds
  // breaks integer and comes back as it was posted, five minutes break between and come back
  // as 300, and an hour and a half is stored as 5400. The style and the script of the field,
  // and the session's token, are left out of what is compared.

  $timeResult = '';

  foreach ( [ 'padForm=time&length=1:30', 'padForm=time&length=300', 'padForm=time&length=5400' ] as $timePost ) {

    $timeCurl = padCurl ( [ 'url'  => $padGoExt . 'request/form_rules_durationpicker&padInclude',
                            'post' => $timePost ] );

    $timeData = preg_replace ( '~<(style|script)\b[^>]*>.*?</\1>|<link\b[^>]*>~s', '', $timeCurl ['data'] );
    $timeData = preg_replace ( '/ value="[0-9a-f]{64}"/', '', $timeData );

    $timeResult .= $timeCurl ['result'] . "\n" . trim ( $timeData ) . "\n\n";

  }

?>
{!timeResult}
