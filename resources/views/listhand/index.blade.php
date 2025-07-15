@extends('layout.index')

@section('content')
<br>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">Danh sách Handresult</h4>
                <p class="text-muted font-14 mb-4">
                    Danh sách các handresult và thống kê, bao gồm số serial, chủ sở hữu, ngày tạo, số lần thắng chi, thua chi, tổng tiền, loại hand, và xếp hạng chi.
                </p>

                <div id="datatable-buttons_wrapper" class="dt-container dt-bootstrap5 dt-empty-footer">
                    <div class="d-md-flex justify-content-between align-items-center my-2">
                        <div class="dt-buttons btn-group flex-wrap">
                            <button class="btn btn-secondary buttons-copy buttons-html5 btn-sm" tabindex="0" aria-controls="datatable-buttons" type="button"><span>Sao chép</span></button>
                            <button class="btn btn-secondary buttons-csv buttons-html5 btn-sm" tabindex="0" aria-controls="datatable-buttons" type="button"><span>CSV</span></button>
                            <button class="btn btn-secondary buttons-excel buttons-html5 btn-sm" tabindex="0" aria-controls="datatable-buttons" type="button"><span>Excel</span></button>
                            <button class="btn btn-secondary buttons-print btn-sm" tabindex="0" aria-controls="datatable-buttons" type="button"><span>In</span></button>
                            <button class="btn btn-secondary buttons-pdf buttons-html5 btn-sm" tabindex="0" aria-controls="datatable-buttons" type="button"><span>PDF</span></button>
                        </div>
                        <div class="dt-search">
                            <label for="dt-search-2">Tìm kiếm:</label>
                            <input type="search" class="form-control form-control-sm" id="dt-search-2" placeholder="Nhập từ khóa..." aria-controls="datatable-buttons">
                        </div>
                    </div>
                    <table id="datatable-buttons" class="table table-striped dt-responsive nowrap w-100 mb-0 dataTable dtr-inline" aria-describedby="datatable-buttons_info" style="width: 100%;">
                        <colgroup>
                            <col data-dt-column="0" style="width: 100px;">
                            <col data-dt-column="1" style="width: 150px;">
                            <col data-dt-column="2" style="width: 120px;">
                            <col data-dt-column="3" style="width: 100px;">
                            <col data-dt-column="4" style="width: 100px;">
                            <col data-dt-column="5" style="width: 120px;">
                            <col data-dt-column="6" style="width: 100px;">
                            <col data-dt-column="7" style="width: 100px;">
                            <col data-dt-column="8" style="width: 100px;">
                            <col data-dt-column="9" style="width: 100px;">
                            <col data-dt-column="10" style="width: 120px;">
                        </colgroup>
                        <thead>
                            <tr>
                                <th data-dt-column="0" class="dt-orderable-asc dt-orderable-desc dt-ordering-asc" aria-sort="ascending">
                                    <div class="dt-column-header"><span class="dt-column-title">Serial</span><span class="dt-column-order" role="button" aria-label="Serial: Sắp xếp" tabindex="0"></span></div>
                                </th>
                                
                                <th data-dt-column="2" class="dt-type-date dt-orderable-asc dt-orderable-desc">
                                    <div class="dt-column-header"><span class="dt-column-title">Ngày tạo</span><span class="dt-column-order" role="button" aria-label="Ngày tạo: Sắp xếp" tabindex="0"></span></div>
                                </th>
                                <th data-dt-column="3" class="dt-type-numeric dt-orderable-asc dt-orderable-desc">
                                    <div class="dt-column-header"><span class="dt-column-title">Thắng Chi</span><span class="dt-column-order" role="button" aria-label="Thắng Chi: Sắp xếp" tabindex="0"></span></div>
                                </th>
                                <th data-dt-column="4" class="dt-type-numeric dt-orderable-asc dt-orderable-desc">
                                    <div class="dt-column-header"><span class="dt-column-title">Thua Chi</span><span class="dt-column-order" role="button" aria-label="Thua Chi: Sắp xếp" tabindex="0"></span></div>
                                </th>
                                <th data-dt-column="5" class="dt-type-numeric dt-orderable-asc dt-orderable-desc">
                                    <div class="dt-column-header"><span class="dt-column-title">Tổng tiền</span><span class="dt-column-order" role="button" aria-label="Tổng tiền: Sắp xếp" tabindex="0"></span></div>
                                </th>
                                <th data-dt-column="6" class="dt-orderable-asc dt-orderable-desc">
                                    <div class="dt-column-header"><span class="dt-column-title">Loại Hand</span><span class="dt-column-order" role="button" aria-label="Loại Hand: Sắp xếp" tabindex="0"></span></div>
                                </th>
                                <th data-dt-column="7" class="dt-type-numeric dt-orderable-asc dt-orderable-desc">
                                    <div class="dt-column-header"><span class="dt-column-title">Xếp hạng Chi 1</span><span class="dt-column-order" role="button" aria-label="Xếp hạng Chi 1: Sắp xếp" tabindex="0"></span></div>
                                </th>
                                <th data-dt-column="8" class="dt-type-numeric dt-orderable-asc dt-orderable-desc">
                                    <div class="dt-column-header"><span class="dt-column-title">Xếp hạng Chi 2</span><span class="dt-column-order" role="button" aria-label="Xếp hạng Chi 2: Sắp xếp" tabindex="0"></span></div>
                                </th>
                                <th data-dt-column="9" class="dt-type-numeric dt-orderable-asc dt-orderable-desc">
                                    <div class="dt-column-header"><span class="dt-column-title">Xếp hạng Chi 3</span><span class="dt-column-order" role="button" aria-label="Xếp hạng Chi 3: Sắp xếp" tabindex="0"></span></div>
                                </th>
                                <th data-dt-column="10" class="dt-orderable-none">
                                    <div class="dt-column-header"><span class="dt-column-title">Hành động</span></div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($handresults as $handresult)
                                <tr>
                                    <td class="sorting_1 dtr-control" tabindex="0">{{ $handresult->device->serial }}</td>
                                  
                                    <td class="dt-type-date">{{ $handresult->created_at ? $handresult->created_at->format('Y/m/d H:i') : 'N/A' }}</td>
                                    <td class="dt-type-numeric">{{ $handresult->chi_wins ?? 0 }}</td>
                                    <td class="dt-type-numeric">{{ $handresult->chi_losses ?? 0 }}</td>
                                    <td class="dt-type-numeric">{{ number_format($handresult->money ?? 0, 2) }}</td>
                                    <td>{{ $handresult->hand_type ?? 'N/A' }}</td>
                                    <td class="dt-type-numeric">{{ $handresult->first_chi_rank ?? 'N/A' }}</td>
                                    <td class="dt-type-numeric">{{ $handresult->middle_chi_rank ?? 'N/A' }}</td>
                                    <td class="dt-type-numeric">{{ $handresult->last_chi_rank ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('hand-results.edit', $handresult->id) }}" class="btn btn-sm btn-primary">Sửa</a>
                                        <form action="{{ route('hand-results.destroy', $handresult->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa bản ghi này?')">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot></tfoot>
                    </table>
                    <div class="d-md-flex justify-content-between align-items-center mt-2">
                        <div class="dt-info" aria-live="polite" id="datatable-buttons_info" role="status">Hiển thị {{ $handresults->count() > 0 ? 1 : 0 }} đến {{ $handresults->count() }} trong tổng số {{ $handresults->total() }} bản ghi</div>
                        <div class="dt-paging">
                            {{ $handresults->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                    <div class="dt-autosize" style="width: 100%; height: 0px;"></div>
                </div>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('#datatable-buttons').DataTable({
            dom: 'Bfrtip',
            buttons: [
                { extend: 'copy', text: 'Sao chép' },
                { extend: 'csv', text: 'CSV' },
                { extend: 'excel', text: 'Excel' },
                { extend: 'print', text: 'In' },
                { extend: 'pdf', text: 'PDF' }
            ],
            responsive: true,
            pageLength: 10,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/vi.json'
            }
        });
    });
</script>
@endsection