<?php

  // The content of a rich field written as a pair is taken as written and never rendered, so
  // the fields written in it are text: the markdown editor's example field, an {pad:input} among
  // the choices of a combobox and a slider inside a tag input carry no rules of the form.

  $text = "{pad:form 'f'}{input:mdeditor 'body', label='Body', rules='required'}Write a field like {pad:input 'email', rules='required|email'/} in a template.{/input:mdeditor}"
        . "{input:combobox 'city', rules='in:Rome,Oslo'}Rome\n{pad:input 'x', rules='required'/}\nOslo{/input:combobox}"
        . "{input:taginput 'tags'}{input:slider 'v', rules='integer'/}{/input:taginput}"
        . "{pad:input 'name', rules='required'/}<button>Save</button>{/pad:form}";

  $rulesRead = json_encode ( padFormRulesOf ( $text ), JSON_UNESCAPED_SLASHES );

?>
