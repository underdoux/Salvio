<template>
  <div class="p-4">
    <h2 class="text-2xl font-semibold mb-4">Reports Dashboard</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white p-4 rounded shadow">
        <h3 class="font-semibold mb-2">Revenue</h3>
        <canvas id="revenueChart"></canvas>
      </div>
      <div class="bg-white p-4 rounded shadow">
        <h3 class="font-semibold mb-2">Commissions</h3>
        <canvas id="commissionChart"></canvas>
      </div>
      <div class="bg-white p-4 rounded shadow">
        <h3 class="font-semibold mb-2">Profit Distributions</h3>
        <canvas id="profitChart"></canvas>
      </div>
    </div>
  </div>
</template>

<script>
import Chart from 'chart.js/auto';

export default {
  mounted() {
    this.renderCharts();
  },
  methods: {
    renderCharts() {
      const revenueCtx = document.getElementById('revenueChart').getContext('2d');
      new Chart(revenueCtx, {
        type: 'line',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
          datasets: [{
            label: 'Revenue',
            data: [1000, 1500, 1200, 1800, 2000],
            borderColor: 'rgba(59, 130, 246, 1)',
            backgroundColor: 'rgba(59, 130, 246, 0.2)',
            fill: true,
          }]
        }
      });

      const commissionCtx = document.getElementById('commissionChart').getContext('2d');
      new Chart(commissionCtx, {
        type: 'bar',
        data: {
          labels: ['User A', 'User B', 'User C'],
          datasets: [{
            label: 'Commissions',
            data: [300, 500, 400],
            backgroundColor: 'rgba(16, 185, 129, 1)',
          }]
        }
      });

      const profitCtx = document.getElementById('profitChart').getContext('2d');
      new Chart(profitCtx, {
        type: 'pie',
        data: {
          labels: ['Investor A', 'Investor B', 'Investor C'],
          datasets: [{
            label: 'Profit Distribution',
            data: [40, 30, 30],
            backgroundColor: [
              'rgba(239, 68, 68, 1)',
              'rgba(234, 179, 8, 1)',
              'rgba(59, 130, 246, 1)',
            ],
          }]
        }
      });
    }
  }
};
</script>

<style scoped>
/* Add any component-specific styles here */
</style>
