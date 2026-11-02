<?php

  // padValidateClient hands the browser's checker the fields each field's rules look at, as
  // watch - from the fields of the rules' files: same:, the required_if family, before: and
  // after: with a field, once each and trimmed - so the checker checks the field again when
  // one of them changes. A field whose rules look at none has no watch; a date written in
  // before: or after: is no field. Beside it the argument of each rule as its message shows it.

  $ruleClient = padValidateClient ( [
    'confirm' => 'required|same:password',
    'address' => 'required_if: delivery , ship , courier|min:5',
    'note'    => 'required_unless:delivery,collect',
    'code'    => 'required_with:phone, fax',
    'phone'   => 'required_without:email|required_with:fax, email',
    'end'     => 'after:start|before:2027-01-01T12:00',
    'start'   => 'before:trip[end]',
    'user'    => 'required|not_in:admin, root',
    'tags[]'  => 'required_if:kinds[],gift'
  ] );

  $ruleVerdicts = [];

  foreach ( $ruleClient as $ruleField => $ruleSpec )
    $ruleVerdicts [] = "$ruleField: " . ( isset ( $ruleSpec ['watch'] ) ? 'watch ' . implode ( ', ', $ruleSpec ['watch'] ) : 'no watch' )
                     . ' - ' . implode ( ' / ', array_map ( fn ( $one ) => "$one[0] $one[3]", $ruleSpec ['rules'] ) );

  $ruleVerdicts = implode ( ' ; ', $ruleVerdicts );

?>
