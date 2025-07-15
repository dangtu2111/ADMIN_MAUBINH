@extends('layout.index')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Hyper</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">Chỉnh sửa Hand Result</li>
                </ol>
            </div>
            <h4 class="page-title">Chỉnh sửa Hand Result #{{ $handResult->id }}</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">Chỉnh sửa thông tin ván chơi</h4>
                <p class="text-muted font-14">Cập nhật thông tin chi tiết của ván chơi</p>
                @if($handResult->chi_wins == 0)
                <input type="hidden" id="result_money" value="{{ abs($handResult->money) / $handResult->chi_losses }}" />
                @else
                <input type="hidden" id="result_money" value="{{ abs($handResult->money) / $handResult->chi_wins }}" />
                @endif
                <!-- Hiển thị thông báo lỗi -->
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Hiển thị thông báo flash -->
                @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif
                @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif

                <ul class="nav nav-tabs nav-bordered mb-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="#edit-handresult-preview" data-bs-toggle="tab" aria-expanded="true" class="nav-link active" aria-selected="true" role="tab">
                            Chỉnh sửa
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active show" id="edit-handresult-preview" role="tabpanel">
                        <form action="{{ route('hand-results.update', $handResult->id) }}" method="POST">
                            @csrf
                            @method('POST')

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label">Loại ván (Hand Type)</label>
                                        <input type="text" name="hand_type" id="hand_type" class="form-control" value="{{ old('hand_type', $handResult->hand_type) }}" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Chi thắng</label>
                                        <input type="number" name="chi_wins" id="chi_wins" class="form-control" value="{{ old('chi_wins', $handResult->chi_wins) }}" min="0" required placeholder="Nhập số chi thắng">
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label">Số tiền thắng thua</label>
                                        <input type="number" name="money" id="money" class="form-control" value="{{ old('money', $handResult->money) }}" step="0.01" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Chi thua</label>
                                        <input type="number" name="chi_losses" id="chi_losses" class="form-control" value="{{ old('chi_losses', $handResult->chi_losses) }}" min="0" required placeholder="Nhập số chi thua">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label class="form-label">Xếp hạng chi đầu</label>
                                        <input type="text" name="first_chi_rank" id="first_chi_rank" class="form-control" value="{{ old('first_chi_rank', $handResult->first_chi_rank) }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label class="form-label">Xếp hạng chi giữa</label>
                                        <input type="text" name="middle_chi_rank" id="middle_chi_rank" class="form-control" value="{{ old('middle_chi_rank', $handResult->middle_chi_rank) }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label class="form-label">Xếp hạng chi cuối</label>
                                        <input type="text" name="last_chi_rank" id="last_chi_rank" class="form-control" value="{{ old('last_chi_rank', $handResult->last_chi_rank) }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                    <label class="form-label">Chi đầu</label>
                                    <input type="text" name="first_hand" id="first_hand" class="form-control" value="{{ old('first_hand', $handResult->gameSession->first_hand) }}" readonly>
                                </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                    <label class="form-label">Chi giữa</label>
                                    <input type="text" name="middle_hand" id="middle_hand" class="form-control" value="{{ old('middle_hand', $handResult->gameSession->middle_hand) }}" readonly>
                                </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                    <label class="form-label">Chi cuối</label>
                                    <input type="text" name="last_hand" id="last_hand" class="form-control" value="{{ old('last_hand', $handResult->gameSession->last_hand) }}" readonly>
                                </div>
                                </div>
                                
                            </div>
                           
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary me-2">Cập nhật</button>
                                <a href="{{ route('listhand.index') }}" class="btn btn-secondary">Hủy</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chiWinsInput = document.getElementById('chi_wins');
        const chiLossesInput = document.getElementById('chi_losses');
        const moneyInput = document.getElementById('money');
        const moneyInitial = Math.abs(parseFloat(document.getElementById('result_money').value));


        function calculateMoney() {
            let chiWins = parseInt(chiWinsInput.value) || 0;
            let chiLosses = parseInt(chiLossesInput.value) || 0;

            // Nếu chi_wins > 0, đặt chi_losses = 0
            if (chiWins > 0) {
                chiLosses = 0;
                chiLossesInput.value = 0;
            }
            // Nếu chi_losses > 0, đặt chi_wins = 0
            if (chiLosses > 0) {
                chiWins = 0;
                chiWinsInput.value = 0;
            }

            // Tính tiền theo công thức
            const money = (chiWins * moneyInitial * 0.98) - (chiLosses * moneyInitial);
            moneyInput.value = money.toFixed(2);
        }

        // Gắn sự kiện input cho chi_wins và chi_losses
        chiWinsInput.addEventListener('input', calculateMoney);
        chiLossesInput.addEventListener('input', calculateMoney);

    });
</script>
@endsection