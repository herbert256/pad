<?php

  if ( ! pad_local() ) 
    return;

  $pad_output = '<center><table border="1" cellpadding="10" cellspacing="0">

<tr>
  <th bgcolor="#dddddd">PHP</th>
  <th bgcolor="#dddddd">HTML</th>
  <th bgcolor="#dddddd">RESULT</th>
</tr>' . $pad_output . '</table></center?'; 


$pad_demo_php = pad_file_get_contents ( PAD_APP . "pages/$page.php");
$pad_demo_php = "<td rowspan=\"$pad_demo_count\" style=\"vertical-align:top\">" .  pad_colors_string ($pad_demo_php) . '</td>';

$pad_output = str_replace('###first###', $pad_demo_php, $pad_output);

?>