<?php

  // A multiselect posts a list, so its rules are read under the name it posts: interests[],
  // which padValidate judges as a list - kept under interests, a list posted there was refused
  // as no single value whatever it held. A name written with its brackets keeps them, the
  // input: type reads the same, and {pad:input} keeps the name it is given.

  $text = "{pad:form 'f'}{input:multiselect 'interests', rules='required|in:PHP,Maps'/}"
        . "{input:multiselect 'days[]', rules='required'}Monday\nTuesday{/input:multiselect}"
        . "{pad:input 'tags', rules='required'/}{/pad:form}";

  $rulesRead = json_encode ( padFormRulesOf ( $text ), JSON_UNESCAPED_SLASHES );

?>
