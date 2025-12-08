import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('orderChart');
    if (!ctx) {
        console.error('Canvas element with id "orderChart" not found');
        return;
    }

    if (!window.orderData) {
        console.error('window.orderData is not defined');
        return;
    }

    const labels = window.orderData.map(item => item.date);
    const data = window.orderData.map(item => item.count);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Pesanan',
                data: data,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
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
