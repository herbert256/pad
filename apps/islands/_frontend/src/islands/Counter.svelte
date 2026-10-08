<script>
  let { start, framework } = $props();
  let count = $state(start);
</script>

<div class="counter">
  <div class="counter-value">{count}</div>
  <div class="counter-buttons">
    <button onclick={() => count--} aria-label="One less">−</button>
    <button onclick={() => count++} aria-label="One more">+</button>
  </div>
  <p class="counter-note">{framework}: $state</p>
</div>
