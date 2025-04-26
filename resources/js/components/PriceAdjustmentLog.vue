<template>
  <div class="p-4">
    <h2 class="text-2xl font-semibold mb-4">Price Adjustment Log</h2>
    <table class="min-w-full border border-gray-300">
      <thead>
        <tr>
          <th class="border px-4 py-2">Order Item</th>
          <th class="border px-4 py-2">User</th>
          <th class="border px-4 py-2">Before Price</th>
          <th class="border px-4 py-2">After Price</th>
          <th class="border px-4 py-2">Reason</th>
          <th class="border px-4 py-2">Date</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="log in logs" :key="log.id">
          <td class="border px-4 py-2">{{ log.order_item.product.name }}</td>
          <td class="border px-4 py-2">{{ log.user.name }}</td>
          <td class="border px-4 py-2">{{ log.before_price }}</td>
          <td class="border px-4 py-2">{{ log.after_price }}</td>
          <td class="border px-4 py-2">{{ log.reason }}</td>
          <td class="border px-4 py-2">{{ new Date(log.created_at).toLocaleDateString() }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  data() {
    return {
      logs: [],
    };
  },
  mounted() {
    // Fetch price adjustment logs from API or backend
    fetch('/api/price-adjustment-logs', {
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      credentials: 'include',
    })
      .then(response => response.json())
      .then(data => {
        this.logs = data;
      })
      .catch(error => {
        console.error('Error fetching price adjustment logs:', error);
      });
  },
};
</script>

<style scoped>
/* Add any component-specific styles here */
</style>
