<template>
  <div class="p-4">
    <h2 class="text-2xl font-semibold mb-4">Product List</h2>
    <input
      v-model="search"
      type="text"
      placeholder="Search products..."
      class="mb-4 p-2 border rounded w-full"
    />
    <table class="min-w-full border border-gray-300">
      <thead>
        <tr>
          <th class="border px-4 py-2">Name</th>
          <th class="border px-4 py-2">Category</th>
          <th class="border px-4 py-2">BPOM Code</th>
          <th class="border px-4 py-2">Price</th>
          <th class="border px-4 py-2">Stock</th>
          <th class="border px-4 py-2">Is By Order</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="product in filteredProducts" :key="product.id">
          <td class="border px-4 py-2">{{ product.name }}</td>
          <td class="border px-4 py-2">{{ product.category_name }}</td>
          <td class="border px-4 py-2">{{ product.bpom_code }}</td>
          <td class="border px-4 py-2">{{ product.price }}</td>
          <td class="border px-4 py-2">{{ product.stock }}</td>
          <td class="border px-4 py-2">{{ product.is_by_order ? 'Yes' : 'No' }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  data() {
    return {
      search: '',
      products: [],
    };
  },
  computed: {
    filteredProducts() {
      if (!this.search) return this.products;
      return this.products.filter((p) =>
        p.name.toLowerCase().includes(this.search.toLowerCase())
      );
    },
  },
  mounted() {
    // Fetch products from API or backend
    // For now, use static data or fetch from an endpoint
    this.products = [
      {
        id: 1,
        name: 'Product A',
        category_name: 'Category 1',
        bpom_code: 'BPOM123',
        price: 100,
        stock: 50,
        is_by_order: false,
      },
      {
        id: 2,
        name: 'Product B',
        category_name: 'Category 2',
        bpom_code: 'BPOM456',
        price: 200,
        stock: 0,
        is_by_order: true,
      },
    ];
  },
};
</script>

<style scoped>
/* Add any component-specific styles here */
</style>
