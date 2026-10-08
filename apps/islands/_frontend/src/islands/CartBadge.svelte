<script>
  // The badge, in Svelte: the count of the store the others write.
  import { store } from '../pad-islands.js';

  let { cart } = $props();
  const shared = store('cart', cart);
  let count = $state(shared.get().count);

  $effect(() => shared.subscribe((value) => { count = value.count; }));
</script>

<span class="cart-badge" title="Svelte">🛒 <strong>{count}</strong></span>

<style>
  .cart-badge { display: inline-flex; gap: 8px; align-items: center; padding: 8px 14px; border-radius: 999px; background: var(--surface); border: 1px solid var(--line); box-shadow: var(--shadow); }
</style>
