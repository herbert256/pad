<?php

  if ( $padWalk[$pad] == 'start' ) {

    $padWalk[$pad] = 'end';

    $padSourcex = padColorsString (trim($padContent));

   return TRUE;

  }

  $padContent = 

    '<table border="1" cellpadding="10" cellspacing="0">' .

      '<tr><th bgcolor="#dddddd">PHP</th></tr>' .

      '<tr>' .
        '<td style="vertical-align:top">' . 
          '<code><span style="color: #000000">' . 
            padColorsFile ( padApp . "$padPage.php" )   .
          '</span></code>' . '
        </td>' .
      '</tr>' .

      '<tr><th bgcolor="#dddddd">PAD</th></tr>' .

      '<tr>' .
        '<td style="vertical-align:top">' . 
          '<code><span style="color: #000000">' . 
            '<!-- START DEMO SOURCE -->' .  
              trim($padSourcex)  . 
            '<!-- END DEMO SOURCE -->' .  
            '</span></code>' . '
        </td>' .
      '</tr>'.

      '<tr><th bgcolor="#dddddd">RESULT</th></tr>' .

      '<tr>' .
        '<td style="vertical-align:top">' . 
          '<code><span style="color: #000000">' . 
            '<!-- START DEMO RESULT -->' .  
              trim($padContent) . 
            '<!-- END DEMO RESULT -->' .  
          '</span></code>' . '
        </td>' .
      '</tr>' .

    '</table>';

?>