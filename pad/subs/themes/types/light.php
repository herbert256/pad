<?php

  // light - the frame's own light colours, kept light whatever the system or the frame's switch
  // says, with the two greys and the soft accent the frame's set has below WCAG AA - its dim
  // text, the side's headings, a link on the accent's tint - darkened to it.

  return [ 'name'   => 'Light',
           'scheme' => 'light',
           'colors' => [ 'bg' => '#f5f6f8', 'panel' => '#ffffff', 'text' => '#1d2330', 'soft' => '#4b5563', 'dim' => '#5f6672', 'line' => '#e3e6eb',
                         'accent' => '#2563eb', 'accent-text' => '#ffffff', 'accent-soft' => '#edf2fe', 'head' => '#eef0f3', 'code' => '#f3f4f6', 'side' => '#ffffff',
                         'side-text' => '#374151', 'side-dim' => '#646c79', 'side-hover' => '#f1f3f6', 'side-line' => '#e3e6eb', 'side-strong' => '#111827', 'menu' => '#ffffff',
                         'ok' => '#15803d', 'ok-bg' => '#dcfce7', 'warn' => '#a14a07', 'warn-bg' => '#fef3c7', 'bad' => '#b91c1c', 'bad-bg' => '#fee2e2' ] ];

?>
