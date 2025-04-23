<template>
  <div class="p-4">
    <h2 class="text-2xl font-semibold mb-4">Insights Dashboard</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="bg-white p-4 rounded shadow">
        <h3 class="font-semibold mb-2">Best Selling Products</h3>
        <ul>
          <li v-for="product in bestSellingProducts" :key="product.id">{{ product.name }}</li>
        </ul>
      </div>
      <div class="bg-white p-4 rounded shadow">
        <h3 class="font-semibold mb-2">Least Selling Products</h3>
        <ul>
          <li v-for="product in leastSellingProducts" :key="product.id">{{ product.name }}</li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      bestSellingProducts: [],
      leastSellingProducts: [],
    };
  },
  mounted() {
    // Fetch insights from API or backend
    // For now, use static data or fetch from an endpoint
    this.bestSellingProducts = [
      { id: 1, name: 'Product A' },
      { id: 2, name: 'Product B' },
    ];
    this.leastSellingProducts = [
      { id: 3, name: 'Product C' },
      { id: 4, name: 'Product D' },
    ];
  },
};
</script>

<style scoped>
/* Add any component-specific styles here */
</style>
