<template>
  <div class="p-4">
    <h2 class="text-2xl font-semibold mb-4">Notifications</h2>
    <table class="min-w-full border border-gray-300">
      <thead>
        <tr>
          <th class="border px-4 py-2">Type</th>
          <th class="border px-4 py-2">Message</th>
          <th class="border px-4 py-2">Sent At</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="notification in notifications" :key="notification.id">
          <td class="border px-4 py-2">{{ notification.type }}</td>
          <td class="border px-4 py-2">{{ notification.message }}</td>
          <td class="border px-4 py-2">{{ notification.sent_at }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  data() {
    return {
      notifications: [],
    };
  },
  mounted() {
    // Fetch notifications from API or backend
    // For now, use static data or fetch from an endpoint
    this.notifications = [
      {
        id: 1,
        type: 'Order Created',
        message: 'Order #12345 has been created.',
        sent_at: '2025-04-01 10:00',
      },
      {
        id: 2,
        type: 'Order Shipped',
        message: 'Order #12344 has been shipped.',
        sent_at: '2025-04-01 12:00',
      },
    ];
  },
};
</script>

<style scoped>
/* Add any component-specific styles here */
</style>
