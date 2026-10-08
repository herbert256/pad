<script setup>
import { ref } from 'vue';

const props = defineProps({ start: Number, framework: String });
const count = ref(props.start);
</script>

<template>
  <div class="counter">
    <div class="counter-value">{{ count }}</div>
    <div class="counter-buttons">
      <button @click="count--" aria-label="One less">−</button>
      <button @click="count++" aria-label="One more">+</button>
    </div>
    <p class="counter-note">{{ framework }}: ref</p>
  </div>
</template>
