<template>
  <div class="p-4 max-w-4xl mx-auto">
    <h2 class="text-2xl font-semibold mb-4">Create Order</h2>
    <form @submit.prevent="submitOrder">
      <div class="mb-4">
        <label class="block mb-1 font-medium">Tax (%)</label>
        <input v-model.number="tax" type="number" min="0" step="0.01" class="w-full p-2 border rounded" />
      </div>

      <div class="mb-4">
        <label class="block mb-1 font-medium">Payment Type</label>
        <select v-model="paymentType" class="w-full p-2 border rounded">
          <option value="cash">Cash</option>
          <option value="installment">Installment</option>
        </select>
      </div>

      <div class="mb-4">
        <h3 class="font-semibold mb-2">Order Items</h3>
        <div v-for="(item, index) in items" :key="index" class="mb-4 border p-4 rounded">
          <div class="mb-2">
            <label class="block mb-1 font-medium">Product</label>
            <input v-model="item.productName" type="text" class="w-full p-2 border rounded" />
          </div>
          <div class="mb-2">
            <label class="block mb-1 font-medium">Original Price</label>
            <input v-model.number="item.originalPrice" type="number" min="0" step="0.01" class="w-full p-2 border rounded" />
          </div>
          <div class="mb-2">
            <label class="block mb-1 font-medium">Adjusted Price</label>
            <input v-model.number="item.adjustedPrice" type="number" min="0" step="0.01" class="w-full p-2 border rounded" />
          </div>
          <div class="mb-2">
            <label class="block mb-1 font-medium">Adjustment Reason</label>
            <input v-model="item.adjustmentReason" type="text" class="w-full p-2 border rounded" />
          </div>
          <button type="button" @click="removeItem(index)" class="text-red-600 hover:underline">Remove Item</button>
        </div>
        <button type="button" @click="addItem" class="mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Add Item</button>
      </div>

      <div>
        <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded hover:bg-green-700">Submit Order</button>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  data() {
    return {
      tax: 0,
      paymentType: 'cash',
      items: [
        {
          productName: '',
          originalPrice: 0,
          adjustedPrice: 0,
          adjustmentReason: '',
        },
      ],
    };
  },
  methods: {
    addItem() {
      this.items.push({
        productName: '',
        originalPrice: 0,
        adjustedPrice: 0,
        adjustmentReason: '',
      });
    },
    removeItem(index) {
      this.items.splice(index, 1);
    },
    submitOrder() {
      // Validate price adjustments and reasons here before submitting
      // For now, just log the order data
      console.log({
        tax: this.tax,
        paymentType: this.paymentType,
        items: this.items,
      });
      alert('Order submitted (mock)');
    },
  },
};
</script>

<style scoped>
/* Add any component-specific styles here */
</style>
