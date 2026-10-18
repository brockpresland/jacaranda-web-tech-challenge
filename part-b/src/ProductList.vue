<script setup>
// `computed` will create the filtered product list in a later step.
import { ref, computed, onMounted } from 'vue'

// The product data loads from public/products.json.
// This gives you a working list to build on. The filtering logic is yours.
const products = ref([])

// Stores the text entered into the product-name search box.
const searchTerm = ref('')

// Stores the active category and initially allows products from every category.
const selectedCategory = ref('all')

onMounted(async () => {
  const res = await fetch('/products.json')
  products.value = await res.json()
})

// Applies the name search and category selection whenever their reactive values change.
const filteredProducts = computed(() => {
  const normalizedSearchTerm = searchTerm.value.trim().toLowerCase()

  return products.value.filter((product) => {
    const matchesSearch = product.name.toLowerCase().includes(normalizedSearchTerm)
    const matchesCategory = selectedCategory.value === 'all'
      || product.category === selectedCategory.value

    return matchesSearch && matchesCategory
  })
})

// Formats numeric rates consistently with exactly two digits after the decimal point.
const formatRate = (rate) => Number(rate).toFixed(2)

// TODO: show how many products match (a result count).
// TODO: show a sensible empty state when nothing matches.
</script>

<template>
  <section>
    <h2>Products</h2>

    <!-- Binds the search text to reactive state as the user types. -->
    <label for="product-search">Search by product name</label>
    <input
      id="product-search"
      v-model="searchTerm"
      type="search"
      placeholder="Search products"
    >

    <!-- Binds the selected category to reactive state for filtering. -->
    <label for="product-category">Category</label>
    <select id="product-category" v-model="selectedCategory">
      <option value="all">All</option>
      <option value="personal">Personal</option>
      <option value="car">Car</option>
      <option value="debt-consolidation">Debt consolidation</option>
    </select>

    <!-- TODO: add a result count and an empty state. -->
    <!-- Renders only products that satisfy both filters and formats their rates. -->
    <ul>
      <li v-for="product in filteredProducts" :key="product.name">
        {{ product.name }} — {{ product.lender }} — {{ formatRate(product.rate) }}%
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
