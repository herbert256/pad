<?php

  // {widget 'modal', 'terms', title='Terms of sale'} ... {/widget} - a widget of the kind the
  // first parameter names, a file of subs/widgets/types/: the same widget as the widget: type
  // writes, {widget:modal 'terms', title='Terms of sale'}, and the bare name, {modal 'terms',
  // title='Terms of sale'}. The widget's own first parameter - the modal's name - is the
  // second here, and is handed on as the first, on every walk the widget asks for.

  $padWidgetKind = is_string ( $padParm ) ? strtolower ( trim ( $padParm ) ) : '';
  $padParm       = $padOpt [$pad] [2] ?? '';

  return include PAD . 'subs/widgets/widget.php';

?>
