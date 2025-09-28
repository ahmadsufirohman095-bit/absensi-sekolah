// Initialize a global object to store chart instances
window.charts = window.charts || {};

function initCharts(data) {
    // Destroy existing chart instances if they exist
    if (window.charts.userDistributionChart) window.charts.userDistributionChart.destroy();
    if (window.charts.attendanceChart) window.charts.attendanceChart.destroy();
    if (window.charts.monthlyAbsenceChart) window.charts.monthlyAbsenceChart.destroy();
    if (window.charts.studentsPerClassChart) window.charts.studentsPerClassChart.destroy();

    // User Distribution Chart
    const userDistributionCanvas = document.getElementById('userDistributionChart');
    if (userDistributionCanvas) {
        const userDistributionCtx = userDistributionCanvas.getContext('2d');
        window.charts.userDistributionChart = new Chart(userDistributionCtx, {
            type: 'pie',
            data: {
                labels: data.totalUsers.labels,
                datasets: [{
                    label: 'Total Pengguna',
                    data: data.totalUsers.data,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Distribusi Pengguna'
                    }
                }
            }
        });
    }

    // Attendance By Subject Chart
    const attendanceCanvas = document.getElementById('attendanceChart');
    if (attendanceCanvas) {
        const attendanceCtx = attendanceCanvas.getContext('2d');
        window.charts.attendanceChart = new Chart(attendanceCtx, {
            type: 'bar',
            data: {
                labels: data.attendanceBySubject.labels,
                datasets: [{
                    label: 'Total Absensi',
                    data: data.attendanceBySubject.data,
                    backgroundColor: data.attendanceBySubject.backgroundColor,
                    borderColor: data.attendanceBySubject.borderColor,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Kehadiran per Mata Pelajaran Hari Ini'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Monthly Absence Chart
    const monthlyAbsenceCanvas = document.getElementById('monthlyAbsenceChart');
    if (monthlyAbsenceCanvas) {
        const monthlyAbsenceCtx = monthlyAbsenceCanvas.getContext('2d');
        window.charts.monthlyAbsenceChart = new Chart(monthlyAbsenceCtx, {
            type: 'bar',
            data: {
                labels: data.monthlyAbsence.labels,
                datasets: [{
                    label: 'Total Absensi',
                    data: data.monthlyAbsence.data,
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Absensi Bulanan'
                    }
                }
            }
        });
    }

    // Students Per Class Chart
    const studentsPerClassCanvas = document.getElementById('studentsPerClassChart');
    if (studentsPerClassCanvas) {
        const studentsPerClassCtx = studentsPerClassCanvas.getContext('2d');
        window.charts.studentsPerClassChart = new Chart(studentsPerClassCtx, {
            type: 'pie',
            data: {
                labels: data.studentsPerClass.labels,
                datasets: [{
                    label: 'Jumlah Siswa',
                    data: data.studentsPerClass.data,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Jumlah Siswa per Kelas'
                    }
                }
            }
        });
    }
}

document.addEventListener('turbo:load', function () {
    // Only attempt to initialize charts if on the admin dashboard page
    if (window.location.pathname === '/dashboard' || window.location.pathname === '/admin/dashboard') {
        function attemptInitCharts() {
            const userDistributionCanvas = document.getElementById('userDistributionChart');
            // Check if at least one canvas element exists before fetching data
            if (userDistributionCanvas) {
                fetch('/admin/dashboard/chart-data') // Use relative path
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        initCharts(data);
                    })
                    .catch(error => {
                        console.error('Error fetching or parsing chart data:', error);
                    });
            } else {
                // If canvas not found, try again on the next animation frame
                requestAnimationFrame(attemptInitCharts);
            }
        }

        // Start attempting to initialize charts
        requestAnimationFrame(attemptInitCharts);
    } else {
        // If not on the dashboard page, destroy any existing chart instances
        if (window.charts.userDistributionChart) window.charts.userDistributionChart.destroy();
        if (window.charts.attendanceChart) window.charts.attendanceChart.destroy();
        if (window.charts.monthlyAbsenceChart) window.charts.monthlyAbsenceChart.destroy();
        if (window.charts.studentsPerClassChart) window.charts.studentsPerClassChart.destroy();
    }
});
