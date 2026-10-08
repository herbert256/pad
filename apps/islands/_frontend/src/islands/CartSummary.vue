<script setup>
// The summary, in Vue: the same store the React shelf writes, read into a ref.
import { ref, onUnmounted } from 'vue';
import { store, post } from '../pad-islands.js';

const props = defineProps({ cart: Object });
const shared = store('cart', props.cart);
const cart = ref(shared.get());
onUnmounted(shared.subscribe((value) => { cart.value = value; }));

async function remove(id) {
  shared.set((await post('examples/shared', { op: 'remove', id })).cart);
}
</script>

<template>
  <div class="card summary">
    <h3>Your cart</h3>
    <p v-if="!cart.lines.length" class="muted small">Empty - add something from the shelf.</p>
    <div v-for="line in cart.lines" :key="line.id" class="line">
      <span>{{ line.emoji }} {{ line.name }} × {{ line.quantity }}</span>
      <button class="btn-icon btn-ghost" @click="remove(line.id)" :aria-label="'One ' + line.name + ' less'">−</button>
    </div>
    <div class="line total"><span>Total</span><span>€ {{ cart.total }}</span></div>
    <p class="framework-tag">Vue</p>
  </div>
</template>

<style scoped>
.summary h3 { margin-top: 0; }
.line { display: flex; justify-content: space-between; align-items: center; gap: 8px; padding: 6px 0; border-bottom: 1px solid var(--line); font-size: 14px; }
.line.total { font-weight: 800; border-bottom: 0; }
</style>
