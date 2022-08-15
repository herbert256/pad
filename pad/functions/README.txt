PHP snippets for
- an option of {$var}:              {$var | xxx | yyy } 
- a function in a PAD eval string:  {if xxx(123) > yyy(456)}

Filenames must be in lowercase

In

  $name         The name of the PHP snippet

  $value        The working value
                - The left side value if applicable
                - The first parameter when given and if no left value is available
                - otherwise $myself (eq the value of $var {$var})

  $padarms        An array of the parms, starting at [0]
  $count        The number of parameters

  $padarm1        The first parameter
  $padarm2        The second parameter
  $padarm3        The third parameter
  etc.

Out

  return xxxx;  The result of the function.

