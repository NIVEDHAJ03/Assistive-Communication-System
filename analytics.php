<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<canvas id="chart"></canvas>

<script>
fetch('analytics_data.php')
.then(response => response.json())
.then(data => {

new Chart(document.getElementById("chart"), {
    type: 'bar',
    data: {
        labels: data.labels,
        datasets: [{
            label: 'Emergency Count',
            data: data.values
        }]
    }
});
});
</script>