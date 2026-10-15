<script setup>
import { ref, onMounted } from 'vue'

// The product data loads from public/products.json.
// This gives you a working list to build on. The filtering logic is yours.
const products = ref([])

onMounted(async () => {
  const res = await fetch('/products.json')
  products.value = await res.json()
})

// TODO: add a reactive search term, bound to a search box, that filters by product name as you type.
// TODO: add a reactive category filter (for example: all, personal, car, debt-consolidation).
// TODO: create a computed list that applies both the search term and the category filter.
// TODO: format each rate to 2 decimal places.
// TODO: show how many products match (a result count).
// TODO: show a sensible empty state when nothing matches.
</script>

<template>
  <section>
    <h2>Products</h2>

    <!-- TODO: search box (filters by name as you type) -->
    <!-- TODO: category filter -->

    <!-- TODO: replace this starter list with your filtered list. -->
    <!-- TODO: show name, lender, and rate (to 2 decimal places). -->
    <!-- TODO: add a result count and an empty state. -->
    <ul>
      <li v-for="product in products" :key="product.name">
        {{ product.name }} — {{ product.lender }} — {{ product.rate }}%
      </li>
    </ul>
  </section>
</template>

<style scoped>
section {
  max-width: 640px;
  margin: 0 auto;
}
</style>
