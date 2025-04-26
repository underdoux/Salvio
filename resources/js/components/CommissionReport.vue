<template>
  <div class="p-4">
    <h2 class="text-2xl font-semibold mb-4">Commission Report</h2>
    <canvas id="commissionChart"></canvas>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import Chart from 'chart.js/auto';

export default {
  setup() {
    const commissionChart = ref(null);

    onMounted(() => {
      const ctx = document.getElementById('commissionChart').getContext('2d');
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['User A', 'User B', 'User C'],
          datasets: [{
            label: 'Commission',
            data: [500, 700, 300],
            backgroundColor: 'rgba(75, 192, 192, 0.6)',
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
      commissionChart
    };
  }
};
</script>

<style scoped>
/* Add any component-specific styles here */
</style>
