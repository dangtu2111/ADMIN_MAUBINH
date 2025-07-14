@extends('layout.index')

@section('content')
<br>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">Device List</h4>
                <p class="text-muted font-14 mb-4">
                    List of devices and their statistics, including serial, owner, creation date, chi wins, chi losses, and total money.
                </p>

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
                            <col data-dt-column="3" style="width: 100px;">
                            <col data-dt-column="4" style="width: 100px;">
                            <col data-dt-column="5" style="width: 150px;">
                        </colgroup>
                        <thead>
                            <tr>
                                <th data-dt-column="0" class="dt-orderable-asc dt-orderable-desc dt-ordering-asc" aria-sort="ascending">
                                    <div class="dt-column-header"><span class="dt-column-title">Serial</span><span class="dt-column-order" role="button" aria-label="Serial: Activate to sort" tabindex="0"></span></div>
                                </th>
                                <th data-dt-column="1" class="dt-orderable-asc dt-orderable-desc">
                                    <div class="dt-column-header"><span class="dt-column-title">Owner</span><span class="dt-column-order" role="button" aria-label="Owner: Activate to sort" tabindex="0"></span></div>
                                </th>
                                <th data-dt-column="2" class="dt-type-date dt-orderable-asc dt-orderable-desc">
                                    <div class="dt-column-header"><span class="dt-column-title">Created At</span><span class="dt-column-order" role="button" aria-label="Created At: Activate to sort" tabindex="0"></span></div>
                                </th>
                                <th data-dt-column="3" class="dt-type-numeric dt-orderable-asc dt-orderable-desc">
                                    <div class="dt-column-header"><span class="dt-column-title">Chi Wins</span><span class="dt-column-order" role="button" aria-label="Chi Wins: Activate to sort" tabindex="0"></span></div>
                                </th>
                                <th data-dt-column="4" class="dt-type-numeric dt-orderable-asc dt-orderable-desc">
                                    <div class="dt-column-header"><span class="dt-column-title">Chi Losses</span><span class="dt-column-order" role="button" aria-label="Chi Losses: Activate to sort" tabindex="0"></span></div>
                                </th>
                                <th data-dt-column="5" class="dt-type-numeric dt-orderable-asc dt-orderable-desc">
                                    <div class="dt-column-header"><span class="dt-column-title">Total Money</span><span class="dt-column-order" role="button" aria-label="Total Money: Activate to sort" tabindex="0"></span></div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($devices as $device)
                                <tr>
                                    <td class="sorting_1 dtr-control" tabindex="0">{{ $device->serial }}</td>
                                    <td>{{ $device->user->username ?? 'N/A' }}</td>
                                    <td class="dt-type-date">{{ $device->created_at ? $device->created_at->format('Y/m/d') : 'N/A' }}</td>
                                    <td class="dt-type-numeric">{{ $device->deviceStats->total_chi_wins ?? 0 }}</td>
                                    <td class="dt-type-numeric">{{ $device->deviceStats->total_chi_losses ?? 0 }}</td>
                                    <td class="dt-type-numeric">{{ number_format($device->deviceStats->total_money ?? 0, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot></tfoot>
                    </table>
                    <div class="d-md-flex justify-content-between align-items-center mt-2">
                        <div class="dt-info" aria-live="polite" id="datatable-buttons_info" role="status">Showing {{ $devices->count() > 0 ? 1 : 0 }} to {{ $devices->count() }} of {{ $devices->count() }} entries</div>
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
    document.addEventListener('DOMContentLoaded', function () {
        $('#datatable-buttons').DataTable({
            dom: 'Bfrtip',
            buttons: ['copy', 'csv', 'excel', 'print', 'pdf'],
            responsive: true,
            pageLength: 10
        });
    });
</script>
@endsection