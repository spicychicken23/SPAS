function displayMonthlyAttendance(attendanceData) {
    var ctx = document.getElementById('monthlyAttendanceChart').getContext('2d');

    var labels = [];
    var attendeesData = [];
    var absenteesData = [];

    attendanceData.forEach(function (day) {
        labels.push(day.attendance_date);
        attendeesData.push(day.total_attendance);
        absenteesData.push(day.total_absentees);
    });

    var data = {
        labels: labels,
        datasets: [
            {
                label: 'Attendees',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
                data: attendeesData,
            },
            {
                label: 'Absentees',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1,
                data: absenteesData,
            },
        ],
    };

    var options = {
        scales: {
            x: {
                type: 'time', // Assuming the labels are dates
                time: {
                    unit: 'week', // Adjust as needed
                },
            },
            y: {
                beginAtZero: true,
            },
        },
    };

    var config = {
        type: 'bar',
        data: data,
        options: options,
    };

    var myChart = new Chart(ctx, config);
}
