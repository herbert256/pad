PHP snippets for
- an option of {$var}:              {$var | xxx | yyy } 
- a function in a pad eval string:  {if xxx(123) > yyy(456)}

In

  $name         The name of the PHP snippet

  $value        The working value
                - The left side value if applicable
                - The first parameter when given and if no left value is available
                - otherwise $myself (eq the value of $var {$var})

  $parms        An array of the parms, starting at [0]
  $count        The number of parameters

Out

  return xxxx;  The result of the function.

