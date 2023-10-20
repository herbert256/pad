<?php
      
 $padContent = trim ($padContent, "\n");
 $padContent = '<pre>' . $padContent . '</pre>';
 
 $padContent = str_replace ("\n\n", "<br>\n", $padContent);
 
?>