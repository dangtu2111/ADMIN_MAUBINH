<div class="card">
    <div class="d-flex card-header justify-content-between align-items-center">
        <h4 class="header-title">Doanh thu theo từng thiết bị</h4>
        <a href="javascript:void(0);" class="btn btn-sm btn-light">Export <i class="mdi mdi-download ms-1"></i></a>
    </div>
    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table table-centered table-nowrap table-hover mb-0">
                <thead>
                    <tr>
                        <th>Thiết bị</th>
                        <th>Tổng tiền</th>
                        <th>Số lượng ván</th>
                        <th>Doanh thu ngày gần nhất</th>
                        <th>Ngày gần nhất</th>
                    </tr>
                </thead>
                <tbody id="device-revenue-table"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
    async function fetchDeviceRevenue() {
        try {
            const response = await fetch('/devices/revenue');
            const result = await response.json();
            if (result.success) {
                const tbody = document.getElementById('device-revenue-table');
                tbody.innerHTML = ''; // Xóa nội dung cũ
                result.data.forEach(device => {
                    const row = `
                            <tr>
                                <td>
                                    <h5 class="font-14 my-1 fw-normal">${device.serial}</h5>
                                    <span class="text-muted font-13">${device.latest_created_at || 'N/A'}</span>
                                </td>
                                <td>
                                    <h5 class="font-14 my-1 fw-normal">${device.total_money.toLocaleString('vi-VN')} VNĐ</h5>
                                    <span class="text-muted font-13">Tổng tiền</span>
                                </td>
                                <td>
                                    <h5 class="font-14 my-1 fw-normal">${device.hand_results_count}</h5>
                                    <span class="text-muted font-13">Số lượng ván</span>
                                </td>
                                <td>
                                    <h5 class="font-14 my-1 fw-normal">${device.latest_date_revenue.toLocaleString('vi-VN')} VNĐ</h5>
                                    <span class="text-muted font-13">Doanh thu ngày gần nhất</span>
                                </td>
                                <td>
                                    <h5 class="font-14 my-1 fw-normal">${device.latest_created_at || 'N/A'}</h5>
                                    <span class="text-muted font-13">Ngày gần nhất</span>
                                </td>
                            </tr>`;
                    tbody.innerHTML += row;
                });
            }
        } catch (error) {
            console.error('Error fetching data:', error);
        }
    }

    // Gọi hàm khi trang tải
    document.addEventListener('DOMContentLoaded', fetchDeviceRevenue);
</script>