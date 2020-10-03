<?php

  $pad_close_parms =
                '{close_parms}' 
              . $pad_parms2
              . '{/close_parms}{'
              . $pad_pair_search
              . '}'
              . $pad_content
              . '{/'
              . $pad_pair_search
              . ' ###%%%close_parms%%%###}' ;

  pad_html ($pad_close_parms);

  return FALSE;

?>