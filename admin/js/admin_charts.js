// Chờ cho DOM tải xong
document.addEventListener("DOMContentLoaded", () => {
  
  // --- 1. Biểu đồ đường (Doanh thu 6 tháng) ---
  const revenueChartCtx = document.getElementById('revenueChart');
  if (revenueChartCtx) {
    new Chart(revenueChartCtx, {
      type: 'line',
      data: {
        labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6'],
        datasets: [{
          label: 'Doanh thu',
          data: [4500000, 4800000, 4200000, 5100000, 4900000, 5500000],
          borderColor: '#007bff',
          backgroundColor: 'rgba(0,123,255,0.1)',
          fill: true,
          tension: 0.4 // Làm cho đường cong mượt mà
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: false // Ẩn legend
          }
        },
        scales: {
          y: {
            beginAtZero: false,
            ticks: {
              // Định dạng lại số cho trục Y
              callback: function(value, index, values) {
                return value / 1000000 + 'tr';
              }
            }
          }
        }
      }
    });
  }

  // --- 2. Biểu đồ cột (Học viên mới) ---
  const newStudentsChartCtx = document.getElementById('newStudentsChart');
  if (newStudentsChartCtx) {
    new Chart(newStudentsChartCtx, {
      type: 'bar',
      data: {
        labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6'],
        datasets: [{
          label: 'Học viên mới',
          data: [45, 52, 60, 71, 65, 82],
          backgroundColor: '#8e2de2', // Màu tím
          borderRadius: 4
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: false
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

  // --- 3. Biểu đồ tròn (Phân bổ khóa học) ---
  const pieChartCtx = document.getElementById('pieChart');
  if (pieChartCtx) {
    new Chart(pieChartCtx, {
      type: 'doughnut', // Kiểu biểu đồ tròn (doughnut có lỗ ở giữa)
      data: {
        labels: ['Lập trình', 'Thiết kế', 'Marketing', 'Quản lý'],
        datasets: [{
          label: 'Phân bổ khóa học',
          data: [45, 25, 20, 10], // Tỷ lệ %
          backgroundColor: [
            '#007bff', // Lập trình (Xanh)
            '#8e2de2', // Thiết kế (Tím)
            '#dc3545', // Marketing (Đỏ/Hồng)
            '#fd7e14'  // Quản lý (Cam)
          ],
          hoverOffset: 4
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'right', // Hiển thị legend bên phải
          }
        }
      }
    });
  }

  // --- 4. Biểu đồ thanh ngang (Top khóa học) ---
  const topCoursesChartCtx = document.getElementById('topCoursesChart');
  if (topCoursesChartCtx) {
    new Chart(topCoursesChartCtx, {
      type: 'bar', // Vẫn là 'bar'
      data: {
        labels: ['React & TypeScript', 'Python cơ bản', 'Digital Marketing', 'UI/UX Design', 'Quản trị dự án'],
        datasets: [{
          label: 'Học viên',
          data: [260, 210, 150, 110, 90],
          backgroundColor: '#dc3545', // Màu hồng
          borderRadius: 4
        }]
      },
      options: {
        indexAxis: 'y', // QUAN TRỌNG: Chuyển trục X và Y
        responsive: true,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          x: {
            beginAtZero: true
          }
        }
      }
    });
  }
});