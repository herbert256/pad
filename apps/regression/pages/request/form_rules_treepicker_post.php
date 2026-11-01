<?php

  // The rules on a tree picker of the template are checked before the page's PHP as those of
  // {input}: a post without a choice breaks required and comes back with none chosen, an item
  // that is a group - no choice with leaves, so no radio posts it - breaks in: and comes back
  // with none shown, and a leaf is stored. The style and the script of the field, and the
  // session's token, are left out of what is compared.

  $topicResult = '';

  foreach ( [ 'padForm=pick', 'padForm=pick&topic=Forms', 'padForm=pick&topic=Rules' ] as $topicPost ) {

    $topicCurl = padCurl ( [ 'url'  => $padGoExt . 'request/form_rules_treepicker&padInclude',
                             'post' => $topicPost ] );

    $topicData = preg_replace ( '~<(style|script)\b[^>]*>.*?</\1>|<link\b[^>]*>~s', '', $topicCurl ['data'] );
    $topicData = preg_replace ( '/ value="[0-9a-f]{64}"/', '', $topicData );

    $topicResult .= $topicCurl ['result'] . "\n" . trim ( $topicData ) . "\n\n";

  }

?>
{!topicResult}
