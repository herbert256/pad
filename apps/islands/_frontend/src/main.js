// The islands of every page, by the name a template gives them in data-island. Each entry
// pairs a component with its framework; nothing of either is fetched before an island of
// that name is on the page and due to load (pad-islands.js).

import './theme.js';
import { islands, react, vue, svelte, preact, solid } from './pad-islands.js';

islands({
  ReactCounter:  react(()  => import('./islands/Counter.react.jsx')),
  VueCounter:    vue(()    => import('./islands/Counter.vue')),
  SvelteCounter: svelte(() => import('./islands/Counter.svelte')),
  PreactCounter: preact(() => import('./islands/Counter.preact.js')),
  SolidCounter:  solid(()  => import('./islands/Counter.solid.jsx')),

  Shelf:         react(()  => import('./islands/Shelf.react.jsx')),
  CartSummary:   vue(()    => import('./islands/CartSummary.vue')),
  CartBadge:     svelte(() => import('./islands/CartBadge.svelte')),

  LoadLog:       preact(() => import('./islands/LoadLog.preact.js')),
  Clock:         solid(()  => import('./islands/Clock.solid.jsx'))
});
