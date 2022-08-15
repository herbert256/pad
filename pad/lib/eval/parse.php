<?php
  
  function padEvalParse (&$result, $eval, $myself='') {
    
    $input  = str_split ( padUnescape($eval) );
    $is_hex = $is_var = $is_str = $is_quote = $is_num = $is_other = FALSE;
    $skip   = $i = 0;
    
    foreach ( $input as $key => $one ) {

      if ($skip) {
        $skip--;
        continue;
      }

      if ( $is_other and $result[$i][0] and preg_match('/^[a-zA-Z0-9_]/', $one) ) {
        $result[$i][0] .= $one;
        continue;
      }
      
      $padrev  = (isset($input[$key-1])) ? $input[$key-1] : '';
      $next  = (isset($input[$key+1])) ? $input[$key+1] : '';
      $next2 = (isset($input[$key+2])) ? $input[$key+2] : '';
      
      if ($one=="\\") {

        if ($next == 'n')
          $next = "\n";
        elseif ($next == 'r')
          $next = "\r";
        elseif ($next == 't')
          $next = "\t";
        elseif ( ! in_array ($next, ["'", '"', "\\"]))
          return "Unsupported \\ char";
          
        if ($is_str or $is_quote) {
          $result [$i] [0] .= $next;
          $skip=1;
        } else
          return "Escape \\ char only allowed inside a string";
      
        continue;

      }
      
      if ($one=="'" and ! $is_quote) {
        
        if (!$is_str) {

          $is_str = TRUE;
          $is_other = FALSE;
          
          $i += 100;
          $result [$i] [0] = '';
          $result [$i] [1] = 'VAL';
          
        } else

          $is_str = FALSE;

        continue;

      }
      
      if ($one=='"' and ! $is_str) {
        
        if (!$is_quote) {

          $is_quote = TRUE;
          $is_other = FALSE;
          
          $i += 100;
          $result [$i] [0] = '';
          $result [$i] [1] = 'VAL';

        } else

          $is_quote = FALSE;
          
        continue;

      }
      
      if ($is_str or $is_quote) {
      
        $result [$i] [0].= $one;
        
        continue;

      }
      
      if ($one == ')' or $one == ']') {
      
        $i += 100;
        $result [$i] [0] = ')';
        $result [$i] [1] = 'close';
        $is_other = FALSE;

        continue;

      }
      
      if ($one == '(' or $one == '[' ) {
      
        $i += 100;
        $result [$i] [0] = '(';
        $result [$i] [1] = 'open';
        $is_other = FALSE;

        continue;

      }
      
      if ($one == '@') {

        $i += 100;
        $result[$i][1] = 'VAL';
        $result[$i][0] = $myself;

        $is_other = FALSE;

        continue;

      }

      if ($one == '$' and $next == '$') {

        $i += 100;
        $result[$i][1] = 'VAL';
        $result[$i][0] = $myself;

        $is_other = FALSE;
        $skip = 1;

        continue;

      }

     if ($one == '$' and $next == '#' ) {

        $is_var   = TRUE;
        $is_other = FALSE;
        $skip = 1;
        
        $i += 100;
        $result[$i][1] = '$';
        $result[$i][0] = '#';

        continue;

      }
     if ($one == '$' and $next == '-' and ctype_xdigit($next2) ) {

        $is_var   = TRUE;
        $is_other = FALSE;
        $skip = 2;
        
        $i += 100;
        $result[$i][1] = '$';
        $result[$i][0] = '-' . $next2;

        continue;

      }


      if ($one == '$' and preg_match('/^[a-zA-Z_]/', $next)) {

        $is_var   = TRUE;
        $is_other = FALSE;
        
        $i += 100;
        $result[$i][1] = '$';
        $result[$i][0] = '';

        continue;

      }
 
      if ($is_var) {
      
        if ($one == ':' ) {
          $result[$i][0] .= $one;
          continue;
        } elseif ($one == '#' ) {
          $result[$i][0] .= $one;
          continue;
        } elseif ( preg_match('/^[a-zA-Z0-9_]/', $one) ) {
          $result[$i][0] .= $one;
          continue;
        } elseif ($one == '.' and preg_match('/^[a-zA-Z0-9_]/', $next)) {
          $result[$i][0] .= $one;
          continue;
        }

        $is_var = FALSE;
        
      }

      if ($one == '0' and strtoupper($next) == 'X' and ctype_xdigit($next2) ) {

        $is_hex   = TRUE;
        $is_other = FALSE;
        
        $i += 100;
        $result[$i][1] = 'hex';
        $result[$i][0] = $next2;
        
        $skip = 2;
        
        continue;
        
      }

      if ( $is_hex ) {
 
        if ( ctype_xdigit($one) ) {
          $result[$i][0] .= $one;
          continue;
        } else
          $is_hex = FALSE;
       
      }

      if ( ! $is_num  and
           (      ctype_digit($one)
             or ( $one == '.' and ctype_digit($next) )
             or ( $one == '-' and ctype_digit($next) )
             or ( $one == '-' and $next == '.' and ctype_digit($next2) )
             or ( $one == '+' and ctype_digit($next) )
             or ( $one == '+' and $next == '.' and ctype_digit($next2) )
           )
         ) {
              
        $is_num = TRUE;
        $i += 100;
        $result[$i][0] = $one;
        $result[$i][1] = 'VAL';
        $is_other = FALSE;
        continue;
        
      }
      
      if ( $is_num ) {

        if ( ctype_digit($one) ) {
          $result[$i][0] .= $one;
          continue;
        }

        if ( $one == '.' and ctype_digit($next) ) {
          $result[$i][0] .= $one;
          continue;
        }
        
        if ( strtoupper($one) == 'E'
             and ( ctype_digit($next)
                   or ( $next == '-' and ctype_digit($next2) )
                   or ( $next == '+' and ctype_digit($next2) ) ) )  {
          $result[$i][0] .= $one . $next;
          $skip=1;
          continue;
        }

        $is_num = FALSE;

      }

      if ( in_array($one.$next, padEval_2) ) {
      
        $i += 100;
        $result [$i] [0] = $one.$next;
        $result [$i] [1] = 'OPR';

        $is_other = FALSE;
        $skip = 1;
        
        continue;

      } elseif ( in_array($one, padEval_1) ) {
      
        $i += 100;
        $result [$i] [0] = $one;
        $result [$i] [1] = 'OPR';
        
        $is_other = FALSE;

        continue;

      }

      if (ctype_space($one)) {
        $is_other = FALSE;
        continue;
      }
      
      if ($one == ',') {
        $is_other = FALSE;
        continue;
      }

      if (!$is_other) {
        $is_other = TRUE;
        $i += 100;
        $result[$i][0] = '';
        $result[$i][1] = 'other';
      }
  
      $result[$i][0] .= $one;

    }

    return '';

  }
  
?>