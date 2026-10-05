<?php

  $broken = padOutputCheckSource ( "<a href=\"?develop/output_check_reads_the_links_written_in_a_template\">here</a>\n<a href=\"?gone\">gone</a> <a href=\"?{\$page}\">built</a>\n<form action=\"{$padRoot}nosuchapp/?x\"></form>", 'regression/framework' );

?>
