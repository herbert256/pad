<?php

  if ( $pad_walk == 'start' ) {

    $pad_demo_source [$pad_lvl] = $pad_content;

    $pad_walk = 'end';

   return TRUE;

  }

  if ( $pad_walk == 'end' ) {

    if ( ! isset($pad_demo_count) )
      $pad_demo_count = 1;
    else
      $pad_demo_count++;

    if ($pad_demo_count == 1)
      $pad_demo_first = '###first###';
    else
      $pad_demo_first = '';

    $pad_walk = 'close';

    return 
      '<tr>' . $pad_demo_first .
        '<td style="vertical-align:top">' .  pad_colors_string (trim($pad_demo_source [$pad_lvl])) . '</td>' .
        '<td style="vertical-align:top">' .  trim($pad_result [$pad_lvl]) .                          '</td>' .
      '</tr>';

  }

  if ( $pad_walk == 'close' ) {

    $pad_demo_php = pad_file_get_contents ( PAD_APP . "pages/$page.php");
    $pad_demo_php = "<td rowspan=\"$pad_demo_count\" style=\"vertical-align:top\">" .  pad_colors_string ($pad_demo_php) . '</td>';

    $pad_output = str_replace('###first###', $pad_demo_php, $pad_output);

  }
 
?>