<script setup>
// Vue's Composition API utilities provide reactive state, derived values, and lifecycle handling.
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

</script>

<template>
  <section class="product-list">
    <!-- Introduces the product finder with the site's compact eyebrow and heading style. -->
    <header class="product-list__header">
      <p class="product-list__eyebrow">Find your loan</p>
      <h2>Products</h2>
    </header>

    <!-- Groups the responsive search and category controls on a pale lavender panel. -->
    <div class="product-list__filters">
      <!-- Binds the search text to reactive state as the user types. -->
      <div class="product-list__field product-list__field--search">
        <label for="product-search">Search by product name</label>
        <input
          id="product-search"
          v-model="searchTerm"
          type="search"
          placeholder="e.g. Personal loan"
        >
      </div>

      <!-- Binds the selected category to reactive state for filtering. -->
      <div class="product-list__field">
        <label for="product-category">Category</label>
        <select id="product-category" v-model="selectedCategory">
          <option value="all">All products</option>
          <option value="personal">Personal</option>
          <option value="car">Car</option>
          <option value="debt-consolidation">Debt consolidation</option>
        </select>
      </div>
    </div>

    <!-- Announces the current number of matches when either filter changes. -->
    <p class="product-list__count" aria-live="polite">
      {{ filteredProducts.length }} {{ filteredProducts.length === 1 ? 'product' : 'products' }} found
    </p>

    <!-- Renders only products that satisfy both filters and formats their rates. -->
    <ul v-if="filteredProducts.length" class="product-list__results">
      <li v-for="product in filteredProducts" :key="product.name">
        <div>
          <h3>{{ product.name }}</h3>
          <p>{{ product.lender }}</p>
        </div>
        <p class="product-list__rate">
          <strong>{{ formatRate(product.rate) }}%</strong>
          <span>p.a.</span>
        </p>
      </li>
    </ul>

    <!-- Replaces the empty list with useful feedback when no products match. -->
    <div v-else class="product-list__empty">
      <h3>No products found</h3>
      <p>Try another search term or choose a different category.</p>
    </div>
  </section>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap');

/* Establishes the Jacaranda type, colour palette, and comfortable content width. */
.product-list {
  width: 100%;
  max-width: 760px;
  box-sizing: border-box;
  margin: 0 auto;
  color: #2f2353;
  font-family: 'DM Sans', Arial, Helvetica, sans-serif;
}

/* Creates the compact heading treatment used throughout the reference site. */
.product-list__header {
  margin-bottom: 24px;
}

.product-list__eyebrow {
  margin: 0 0 6px;
  color: #8233ff;
  font-size: 13px;
  font-weight: 700;
  letter-spacing: 1.2px;
  text-transform: uppercase;
}

h2 {
  margin: 0;
  font-size: clamp(32px, 6vw, 48px);
  line-height: 1.08;
}

/* Presents the filter controls on the site's pale lavender surface. */
.product-list__filters {
  display: grid;
  grid-template-columns: minmax(0, 1.5fr) minmax(190px, 1fr);
  gap: 16px;
  padding: 24px;
  background: #f7f5ff;
  border-radius: 20px;
}

.product-list__field {
  display: flex;
  flex-direction: column;
  gap: 6px;
  min-width: 0;
}

label {
  font-size: 13px;
  font-weight: 700;
  line-height: 20px;
}

input,
select {
  width: 100%;
  min-height: 50px;
  box-sizing: border-box;
  border: 2px solid #d5d3dd;
  border-radius: 12px;
  background-color: #fff;
  color: #2f2353;
  font: inherit;
  font-size: 16px;
  padding: 11px 16px;
  transition: border-color 0.2s, box-shadow 0.2s;
}

input::placeholder {
  color: #9791a9;
}

input:focus,
select:focus {
  border-color: #8233ff;
  outline: none;
  box-shadow: 0 0 0 4px #e6d6ff;
}

/* Separates the lightweight result summary from the filter panel and cards. */
.product-list__count {
  margin: 20px 0 12px;
  color: #594f75;
  font-size: 14px;
  font-weight: 500;
}

/* Adapts the reference site's dark, rounded product tiles for searchable results. */
.product-list__results {
  display: grid;
  gap: 12px;
  margin: 0;
  padding: 0;
  list-style: none;
}

.product-list__results li {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
  min-height: 76px;
  box-sizing: border-box;
  padding: 20px 24px;
  border-radius: 20px;
  background: radial-gradient(84% 100% at 100% 100%, #8233ff66 0%, #8233ff00 100%), #2f2353;
  color: #fff;
}

.product-list__results h3,
.product-list__results p {
  margin: 0;
}

.product-list__results h3 {
  font-size: 18px;
  line-height: 1.35;
}

.product-list__results li > div p {
  margin-top: 4px;
  color: #e6d6ff;
  font-size: 14px;
}

.product-list__results li > div {
  min-width: 0;
}

.product-list__rate {
  flex-shrink: 0;
  min-width: 76px;
  text-align: right;
}

.product-list__rate strong,
.product-list__rate span {
  display: block;
}

.product-list__rate strong {
  color: #fee113;
  font-size: 20px;
  line-height: 1.2;
}

.product-list__rate span {
  margin-top: 2px;
  color: #e6d6ff;
  font-size: 12px;
}

/* Gives the no-results message the same soft-card treatment as the controls. */
.product-list__empty {
  padding: 36px 24px;
  border: 2px dashed #c199ff;
  border-radius: 20px;
  background: #f7f5ff;
  text-align: center;
}

.product-list__empty h3,
.product-list__empty p {
  margin: 0;
}

.product-list__empty p {
  margin-top: 6px;
  color: #594f75;
}

/* Stacks filters and tightens cards for narrow mobile screens. */
@media (max-width: 600px) {
  .product-list__filters {
    grid-template-columns: 1fr;
    padding: 20px;
  }

  .product-list__results li {
    gap: 16px;
    padding: 18px 20px;
  }
}
</style>
