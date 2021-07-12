<?php
  
  function pad_parms_parse ($parms) {
    
    $result = [];

    $input  = str_split ( pad_unescape($parms) );
    $in_idx = $in_single = $in_double = $in_var = $in_unknow = $in_arr = $in_eval = FALSE;
    $skip   = $i = 0;
    
    $flag = $var = $eval = $unknow = $idx = $string = $double = $var = $last = '';

    $in_unknow = TRUE;

    foreach ( $input as $key => $one ) {

      if ($skip) {
        $skip--;
        continue;
      }

      if ( ctype_space($one) and ! ($in_single or $in_double) ) {
        continue;
      }

      if ( $one == '=' and ! ($in_single or $in_double) ) {

        if ( $in_unknow and pad_valid_name ($unknow) ) {
          $flag = $unknow;
          $var = $eval = '';
          $in_eval = TRUE;
          $last = 
          continue;
        }

        if ( $in_unknow and substr($unkonw, 0, 1) == '$' and pad_valid_name(substr($unkonw, 1)) ) {
          $var = $unknow;
          $flag = $eval = '';
          $in_eval = TRUE;
          continue;
        }

        pad_error ('oops');

      }

      if ( $one == ',' and ! ($in_single or $in_double) ) {

        if ( $in_eval and $var ) {
          continue;
        }

        if ( $in_eval and $flag ) {
          continue;
        }

        pad_error ('oops');

      }

      if ( ($one == '(' or $one == '[') and  ! ($in_single or $in_double) )  {
        
      }

      if ( ($one == ')' or $one == ']') and  ! ($in_single or $in_double) )  {
        
      }

      if ( $in_single ) {
        $single .= $one;
        continue;
      }

      if ( $in_double ) {
        $double .= $one;
        continue;
      }

      if ( $in_unknow ) {
        $in_unknow .= $one;
        continue;
      }

      if ( $in_eval ) {
        $in_eval .= $one;
        continue;
      }

      pad_error ('oops');

    }

    if ( $eval and ($var or $flag) ) {

    }




      if ( $in_other and $result[$i][0] and preg_match('/^[a-zA-Z0-9_]/', $one) ) {
        $result[$i][0] .= $one;
        continue;
      }
      
      $prev  = (isset($input[$key-1])) ? $input[$key-1] : '';
      $next  = (isset($input[$key+1])) ? $input[$key+1] : '';
      $next2 = (isset($input[$key+2])) ? $input[$key+2] : '';
      
      if ($one=='\\') {

        if ($next == 'n')
          $next = "\n";
        elseif ($next == 'r')
          $next = "\r";
        elseif ($next == 't')
          $next = "\t";
        elseif ( ! in_array ($next, ["'", '"', "\\"]))
          return pad_error ("Unsupported \\ char");
          
        if ($in_single or $in_double) {
          $result [$i] [0] .= $next;
          $skip=1;
        } else
          return pad_error ("Escape \\ char only allowed inside a string");
      
        continue;

      }
      
      if ($one=="'" and ! $in_double) {
        
        if (!$in_single) {

          $in_single = TRUE;
          $in_idx = FALSE;
          
          $i += 100;
          $result [$i] [0] = '';
          $result [$i] [1] = 'VAL';
          
        } else

          $in_single = FALSE;

        continue;

      }
      
      if ($one=='"' and ! $in_single) {
        
        if (!$in_double) {

          $in_double = TRUE;
          $in_idx   = FALSE;
          
          $i += 100;
          $result [$i] [0] = '';
          $result [$i] [1] = 'VAL';

        } else

          $in_double = FALSE;
          
        continue;

      }
      
      if ($in_single or $in_double) {
      
        $result [$i] [0].= $one;
        
        continue;

      }
      
      if ($one == ')' or $one == ']') {
      
        $i += 100;
        $result [$i] [0] = ')';
        $result [$i] [1] = 'close';
        $in_other = FALSE;

        continue;

      }
      
      if ($one == '(' or $one == '[' ) {
      
        $i += 100;
        $result [$i] [0] = '(';
        $result [$i] [1] = 'open';
        $in_other = FALSE;

        continue;

      }
      
      if ($one == '@') {

        $i += 100;
        $result[$i][1] = 'VAL';
        $result[$i][0] = $myself;

        $in_other = FALSE;

        continue;

      }

      if ($one == '$' and $next == '$') {

        $i += 100;
        $result[$i][1] = 'VAL';
        $result[$i][0] = $myself;

        $in_other = FALSE;
        $skip = 1;

        continue;

      }

     if ($one == '$' and $next == '#' ) {

        $in_var   = TRUE;
        $in_other = FALSE;
        $skip = 1;
        
        $i += 100;
        $result[$i][1] = '$';
        $result[$i][0] = '#';

        continue;

      }
     if ($one == '$' and $next == '-' and ctype_xdigit($next2) ) {

        $in_var   = TRUE;
        $in_other = FALSE;
        $skip = 2;
        
        $i += 100;
        $result[$i][1] = '$';
        $result[$i][0] = '-' . $next2;

        continue;

      }


      if ($one == '$' and preg_match('/^[a-zA-Z_]/', $next)) {

        $in_var   = TRUE;
        $in_other = FALSE;
        
        $i += 100;
        $result[$i][1] = '$';
        $result[$i][0] = '';

        continue;

      }
 
      if ($in_var) {
      
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

        $in_var = FALSE;
        
      }

      if ($one == '0' and strtoupper($next) == 'X' and ctype_xdigit($next2) ) {

        $in_hex   = TRUE;
        $in_other = FALSE;
        
        $i += 100;
        $result[$i][1] = 'hex';
        $result[$i][0] = $next2;
        
        $skip = 2;
        
        continue;
        
      }

      if ( $in_hex ) {
 
        if ( ctype_xdigit($one) ) {
          $result[$i][0] .= $one;
          continue;
        } else
          $in_hex = FALSE;
       
      }

      if ( ! $in_num  and
           (      ctype_digit($one)
             or ( $one == '.' and ctype_digit($next) )
             or ( $one == '-' and ctype_digit($next) )
             or ( $one == '-' and $next == '.' and ctype_digit($next2) )
             or ( $one == '+' and ctype_digit($next) )
             or ( $one == '+' and $next == '.' and ctype_digit($next2) )
           )
         ) {
              
        $in_num = TRUE;
        $i += 100;
        $result[$i][0] = $one;
        $result[$i][1] = 'VAL';
        $in_other = FALSE;
        continue;
        
      }
      
      if ( $in_num ) {

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

        $in_num = FALSE;

      }

      if ( in_array($one.$next, pad_eval_2) ) {
      
        $i += 100;
        $result [$i] [0] = $one.$next;
        $result [$i] [1] = 'OPR';

        $in_other = FALSE;
        $skip = 1;
        
        continue;

      } elseif ( in_array($one, pad_eval_1) ) {
      
        $i += 100;
        $result [$i] [0] = $one;
        $result [$i] [1] = 'OPR';
        
        $in_other = FALSE;

        continue;

      }

      if (ctype_space($one)) {
        $in_other = FALSE;
        continue;
      }
      
      if ($one == ',') {
        $in_other = FALSE;
        continue;
      }

      if (!$in_other) {
        $in_other = TRUE;
        $i += 100;
        $result[$i][0] = '';
        $result[$i][1] = 'other';
      }
  
      $result[$i][0] .= $one;

    }

    return $result;

  }
  
?>