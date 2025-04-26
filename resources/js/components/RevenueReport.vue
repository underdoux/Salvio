<template>
  <div class="p-4">
    <h2 class="text-2xl font-semibold mb-4">Revenue Report</h2>
    <canvas id="revenueChart"></canvas>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import Chart from 'chart.js/auto';

export default {
  setup() {
    const revenueChart = ref(null);

    onMounted(() => {
      const ctx = document.getElementById('revenueChart').getContext('2d');
      new Chart(ctx, {
        type: 'line',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
          datasets: [{
            label: 'Revenue',
            data: [1200, 1900, 3000, 5000, 2300, 3400],
            borderColor: 'rgba(54, 162, 235, 1)',
            fill: false,
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
    });

    return {
      revenueChart
    };
  }
};
</script>

<style scoped>
/* Add any component-specific styles here */
</style>
