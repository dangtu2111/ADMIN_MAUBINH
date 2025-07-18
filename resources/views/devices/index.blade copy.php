@extends('layout.index')

@section('content')
<br>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">Danh sách doanh thu mới nhất của thiết bị</h4>
                <p class="text-muted font-14 mb-4">
                    Danh sách các thiết bị với bản ghi doanh thu mới nhất, bao gồm serial, chủ sở hữu, ngày, giờ, tổng tiền và ID HandResult.
                </p>



                <!-- Form tính toán đối chiếu doanh thu -->
                <h5 class="mt-4">Tính toán và đối chiếu doanh thu</h5>
                <form method="GET" action="{{ route('devices.compare-money') }}" class="mb-4">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label for="serial" class="form-label">Device Serial</label>
                            <select class="select2 form-control" id="serial" name="serial" required>
                                <option value="" disabled selected>Chọn thiết bị</option>
                                @foreach ($devices as $device)
                                <option value="{{ $device->serial }}">{{ $device->serial }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="start_hand_result_id" class="form-label">ID HandResult bắt đầu</label>
                            <select class="select2 form-control" id="start_hand_result_id" data-toggle="select2" name="start_hand_result_id" required>
                                <option value="" disabled selected>Chọn ID HandResult</option>
                                @foreach ($latestRevenues as $revenue)
                                @if ($revenue->id_hand_result)
                                <option value="{{ $revenue->id_hand_result }}">
                                    {{ $revenue->id_hand_result }} ({{ $revenue->date }} {{ str_pad($revenue->hour, 2, '0', STR_PAD_LEFT) }}:00)
                                </option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="end_hand_result_id" class="form-label">ID HandResult kết thúc</label>
                            <select class="select2 form-control" id="end_hand_result_id" name="end_hand_result_id" required>
                                <option value="" disabled selected>Chọn ID HandResult</option>
                                @foreach ($latestRevenues as $revenue)
                                @if ($revenue->id_hand_result)
                                <option value="{{ $revenue->id_hand_result }}">
                                    {{ $revenue->id_hand_result }} ({{ $revenue->date }} {{ str_pad($revenue->hour, 2, '0', STR_PAD_LEFT) }}:00)
                                </option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 align-self-end">
                            <button type="submit" class="btn btn-primary">Tính toán</button>
                        </div>
                    </div>
                </form>

                <!-- Hiển thị kết quả đối chiếu nếu có -->
                @if (isset($data))
                <h5>Kết quả đối chiếu doanh thu</h5>
                <div class="row">
                    <div class="col-md-12">
                        <h6>Khoảng ID HandResult: {{ $data['start_hand_result_id'] }} - {{ $data['end_hand_result_id'] }} (Thời gian: {{ $data['start_time'] }} - {{ $data['end_time'] }})</h6>
                        <p>Tổng DeviceHourlyRevenue: {{ number_format($data['total_money'], 2) }} VNĐ</p>
                        <p>Tổng HandResult: {{ number_format($data['hand_result_total'], 2) }} VNĐ</p>
                        <p>Chênh lệch: {{ number_format($data['difference'], 2) }} VNĐ</p>
                        <div class="col-md-3 align-self-end">
                            <button type="button" class="btn btn-info" id="show-handresults">Show HandResults</button>
                        </div>
                        <br>
                        <div id="handresults-list" class="mt-4" style="display: none;">
                            <h5>Danh sách HandResult</h5>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Serial</th>
                                        <th>Ngày tạo</th>
                                        <th>Tiền</th>
                                        <th>Chi thắng</th>
                                        <th>Chi thua</th>
                                    </tr>
                                </thead>
                                <tbody id="handresults-body"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif


                <!-- Bảng danh sách doanh thu mới nhất -->
                <div id="datatable-buttons_wrapper" class="dt-container dt-bootstrap5 dt-empty-footer">
                    <div class="d-md-flex justify-content-between align-items-center my-2">
                        <div class="dt-buttons btn-group flex-wrap">
                            <button class="btn btn-secondary buttons-copy buttons-html5 btn-sm" tabindex="0" aria-controls="datatable-buttons" type="button"><span>Copy</span></button>
                            <button class="btn btn-secondary buttons-csv buttons-html5 btn-sm" tabindex="0" aria-controls="datatable-buttons" type="button"><span>CSV</span></button>
                            <button class="btn btn-secondary buttons-excel buttons-html5 btn-sm" tabindex="0" aria-controls="datatable-buttons" type="button"><span>Excel</span></button>
                            <button class="btn btn-secondary buttons-print btn-sm" tabindex="0" aria-controls="datatable-buttons" type="button"><span>Print</span></button>
                            <button class="btn btn-secondary buttons-pdf buttons-html5 btn-sm" tabindex="0" aria-controls="datatable-buttons" type="button"><span>PDF</span></button>
                        </div>
                        <div class="dt-search">
                            <label for="dt-search-2">Search:</label>
                            <input type="search" class="form-control form-control-sm" id="dt-search-2" placeholder="" aria-controls="datatable-buttons">
                        </div>
                    </div>
                    <table id="datatable-buttons" class="table table-striped dt-responsive nowrap w-100 mb-0 dataTable dtr-inline" aria-describedby="datatable-buttons_info" style="width: 100%;">
                        <colgroup>
                            <col data-dt-column="0" style="width: 150px;">
                            <col data-dt-column="1" style="width: 150px;">
                            <col data-dt-column="2" style="width: 150px;">
                            <col data-dt-column="3" style="width: 120px;">
                            <col data-dt-column="4" style="width: 80px;">
                            <col data-dt-column="5" style="width: 150px;">
                            <col data-dt-column="6" style="width: 120px;">
                            <col data-dt-column="7" style="width: 120px;">
                        </colgroup>
                        <thead>
                            <tr>
                                <th data-dt-column="0" class="dt-orderable-asc dt-orderable-desc dt-ordering-asc" aria-sort="ascending">
                                    <div class="dt-column-header"><span class="dt-column-title">Serial</span><span class="dt-column-order" role="button" aria-label="Serial: Activate to sort" tabindex="0"></span></div>
                                </th>
                                <th data-dt-column="1" class="dt-orderable-asc dt-orderable-desc">
                                    <div class="dt-column-header"><span class="dt-column-title">Chủ sở hữu</span><span class="dt-column-order" role="button" aria-label="Owner: Activate to sort" tabindex="0"></span></div>
                                </th>
                                <th data-dt-column="2" class="dt-type-date dt-orderable-asc dt-orderable-desc">
                                    <div class="dt-column-header"><span class="dt-column-title">Ngày</span><span class="dt-column-order" role="button" aria-label="Date: Activate to sort" tabindex="0"></span></div>
                                </th>
                                <th data-dt-column="3" class="dt-type-numeric dt-orderable-asc dt-orderable-desc">
                                    <div class="dt-column-header"><span class="dt-column-title">Giờ</span><span class="dt-column-order" role="button" aria-label="Hour: Activate to sort" tabindex="0"></span></div>
                                </th>
                                <th data-dt-column="4" class="dt-type-numeric dt-orderable-asc dt-orderable-desc">
                                    <div class="dt-column-header"><span class="dt-column-title">Tổng tiền (VNĐ)</span><span class="dt-column-order" role="button" aria-label="Total Money: Activate to sort" tabindex="0"></span></div>
                                </th>
                                <th data-dt-column="5" class="dt-orderable-asc dt-orderable-desc">
                                    <div class="dt-column-header"><span class="dt-column-title">ID HandResult</span><span class="dt-column-order" role="button" aria-label="ID HandResult: Activate to sort" tabindex="0"></span></div>
                                </th>
                                <th data-dt-column="6" class="dt-orderable-none">
                                    <div class="dt-column-header"><span class="dt-column-title">Hành động</span></div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($latestRevenues as $revenue)
                            <tr>
                                <td class="sorting_1 dtr-control" tabindex="0">{{ $revenue->serial }}</td>
                                <td>{{ $revenue->owner }}</td>
                                <td>{{ $revenue->date }}</td>
                                <td>{{ $revenue->hour }}h</td>
                                <td>{{ number_format($revenue->total_money, 2) }}</td>
                                <td>{{ $revenue->id_hand_result }}</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-primary">Sửa</a>
                                    <form action="{{ route('revenues.destroy', $revenue->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa bản ghi này?')">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6">Không có dữ liệu</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot></tfoot>
                    </table>
                    <div class="d-md-flex justify-content-between align-items-center mt-2">
                        <div class="dt-info" aria-live="polite" id="datatable-buttons_info" role="status">Hiển thị {{ $latestRevenues->count() > 0 ? 1 : 0 }} đến {{ $latestRevenues->count() }} của {{ $latestRevenues->count() }} bản ghi</div>
                        <div class="dt-paging">
                            <nav aria-label="pagination">
                                <ul class="pagination">
                                    <li class="dt-paging-button page-item disabled"><button class="page-link first" role="link" type="button" aria-controls="datatable-buttons" aria-disabled="true" aria-label="First" data-dt-idx="first" tabindex="-1"><i class="ri-arrow-left-double-line align-middle"></i></button></li>
                                    <li class="dt-paging-button page-item disabled"><button class="page-link previous" role="link" type="button" aria-controls="datatable-buttons" aria-disabled="true" aria-label="Previous" data-dt-idx="previous" tabindex="-1"><i class="ri-arrow-left-s-line align-middle"></i></button></li>
                                    <li class="dt-paging-button page-item active"><button class="page-link" role="link" type="button" aria-controls="datatable-buttons" aria-current="page" data-dt-idx="0">1</button></li>
                                    <li class="dt-paging-button page-item"><button class="page-link next" role="link" type="button" aria-controls="datatable-buttons" aria-label="Next" data-dt-idx="next"><i class="ri-arrow-right-s-line align-middle"></i></button></li>
                                    <li class="dt-paging-button page-item"><button class="page-link last" role="link" type="button" aria-controls="datatable-buttons" aria-label="Last" data-dt-idx="last"><i class="ri-arrow-right-double-line align-middle"></i></button></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                    <div class="dt-autosize" style="width: 100%; height: 0px;"></div>
                </div>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // $('#datatable-buttons').DataTable({
        //     dom: 'Bfrtip',
        //     buttons: ['copy', 'csv', 'excel', 'print', 'pdf'],
        //     responsive: true,
        //     pageLength: 10
        // });

        // Khởi tạo Select2 cho form chọn thiết bị và ID HandResult
        $('.select2').select2({
            placeholder: "Chọn",
            allowClear: true
        });

        // Validate client-side để đảm bảo end_hand_result_id >= start_hand_result_id
        $('#end_hand_result_id').on('change', function() {
            let startId = parseInt($('#start_hand_result_id').val());
            let endId = parseInt($(this).val());
            if (endId < startId) {
                alert('ID HandResult kết thúc phải lớn hơn hoặc bằng ID HandResult bắt đầu.');
                $(this).val('');
            }
        });

        // Lọc ID HandResult theo serial được chọn
        $('#serial').on('change', function() {
            let selectedSerial = $(this).val();
            let startHandResultSelect = $('#start_hand_result_id');
            let endHandResultSelect = $('#end_hand_result_id');

            // Xóa các tùy chọn ID HandResult hiện tại
            startHandResultSelect.find('option:not(:first)').remove();
            endHandResultSelect.find('option:not(:first)').remove();

            // Lấy dữ liệu ID HandResult từ server dựa trên serial
            $.ajax({
                url: '{{ route("devices.get-revenues-by-serial") }}',
                method: 'GET',
                data: {
                    serial: selectedSerial
                },
                success: function(response) {
                    response.revenues.forEach(function(revenue) {
                        if (revenue.id_hand_result) {
                            let option = `<option value="${revenue.id_hand_result}">${revenue.id_hand_result} (${revenue.date} ${revenue.hour.toString().padStart(2, '0')}:00)</option>`;
                            startHandResultSelect.append(option);
                            endHandResultSelect.append(option);
                        }
                    });
                },
                error: function() {
                    alert('Không thể tải dữ liệu ID HandResult.');
                }
            });
        });
        $('#show-handresults').on('click', function() {
            let serial = $('#serial').val();
            let startId = $('#start_hand_result_id').val();
            let endId = $('#end_hand_result_id').val();

            if (!serial || !startId || !endId) {
                alert("Vui lòng chọn đầy đủ thông tin!");
                return;
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '{{ route("hand-results.range") }}',
                method: 'GET',
                data: {
                    serial: serial,
                    start_hand_result_id: startId,
                    end_hand_result_id: endId
                },
                success: function(response) {
                    if (response.success) {
                        let body = $('#handresults-body');
                        body.empty();
                        response.data.forEach(hr => {
                            body.append(`
                        <tr>
                            <td>${hr.id}</td>
                            <td>${serial}</td>
                            <td>${hr.created_at}</td>
                            <td>${parseFloat(hr.money).toFixed(2)}</td>
                            <td>${hr.chi_wins ?? 0}</td>
                            <td>${hr.chi_losses ?? 0}</td>
                        </tr>
                    `);
                        });
                        $('#handresults-list').show();
                    } else {
                        alert("Không có dữ liệu.");
                    }
                },
                error: function() {
                    alert("Lỗi khi gọi API.");
                }
            });
        });

    });
</script>
@endsection