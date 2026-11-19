@if (session('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
        <div class="d-flex">
            <div><i class="ti ti-circle-check fs-2 me-2"></i></div>
            <div>{{ session('success') }}</div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></a>
    </div>
@endif
@if ($errors->has('days'))
    <div class="alert alert-danger alert-dismissible" role="alert">
        <div class="d-flex"><div><i class="ti ti-alert-circle fs-2 me-2"></i></div><div>{{ $errors->first('days') }}</div></div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></a>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible" role="alert">
        <div class="d-flex">
            <div><i class="ti ti-alert-circle fs-2 me-2"></i></div>
            <div>{{ session('error') }}</div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></a>
    </div>
@endif
