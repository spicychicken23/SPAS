// daily.js

document.addEventListener("DOMContentLoaded", function () {
    var ctx = document.getElementById('attendanceChart').getContext('2d');

    var attendanceChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: timeLabels,
            datasets: [{
                label: 'Clocked Attendance',
                data: attendanceData,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                x: {
                    type: 'linear',
                    position: 'bottom',
                    min: 6,
                    max: 9,
                    stepSize: 0.0833333, // 5 minutes in hours
                    ticks: {
                        stepSize: 0.0833333,
                        callback: function (value, index) {
                            return (value % 1 === 0) ? `${value}:00` : '';
                        }
                    }
                },
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
