{{-- Modal form perpanjangan peminjaman. Dibuka oleh tombol dengan atribut data-extend. --}}
<div class="modal modal-blur fade" id="extend-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form class="modal-content" method="POST" id="extend-form" novalidate>
            @csrf
            @method('PATCH')
            <div class="modal-header">
                <h5 class="modal-title">Perpanjang Peminjaman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="text-secondary small">Barang</div>
                    <div class="fw-bold" id="extend-item"></div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="text-secondary small">Batas kembali saat ini</div>
                        <div id="extend-current"></div>
                    </div>
                    <div class="col-6">
                        <div class="text-secondary small">Batas kembali baru</div>
                        <div class="fw-bold text-primary" id="extend-new"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label required" for="extend-days">Perpanjang berapa hari?</label>
                    <input type="number" id="extend-days" name="days" min="1" max="{{ config('inventa.extension_days') }}" value="{{ config('inventa.extension_days') }}" class="form-control" placeholder="Maksimal {{ config('inventa.extension_days') }} hari" required>
                    <div class="form-hint">Maksimal {{ config('inventa.extension_days') }} hari per perpanjangan, dan {{ setting('max_extensions') }} kali untuk setiap peminjaman.</div>
                </div>
                <div class="mb-0">
                    <label class="form-label" for="extend-reason">Alasan perpanjangan</label>
                    <textarea id="extend-reason" name="reason" rows="2" class="form-control" placeholder="Opsional, contoh: masih dipakai untuk kegiatan ekskul"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Perpanjang</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        (function () {
            var bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            var maxDays = {{ (int) config('inventa.extension_days') }};
            var due = null;
            function fmt(d) { return d.getDate() + ' ' + bulan[d.getMonth()] + ' ' + d.getFullYear(); }
            function render() {
                var input = document.getElementById('extend-days');
                var days = Math.min(maxDays, Math.max(0, parseInt(input.value, 10) || 0));
                if (!due) { return; }
                var next = new Date(due.getTime());
                next.setDate(next.getDate() + days);
                document.getElementById('extend-new').textContent = days > 0 ? fmt(next) : '-';
            }
            document.addEventListener('click', function (e) {
                var btn = e.target.closest('[data-extend]');
                if (!btn) { return; }
                due = new Date(btn.dataset.due + 'T00:00:00');
                document.getElementById('extend-form').action = btn.dataset.action;
                document.getElementById('extend-item').textContent = btn.dataset.item;
                document.getElementById('extend-current').textContent = fmt(due);
                document.getElementById('extend-days').value = maxDays;
                document.getElementById('extend-reason').value = '';
                render();
                bootstrap.Modal.getOrCreateInstance(document.getElementById('extend-modal')).show();
            });
            document.getElementById('extend-days').addEventListener('input', render);
        })();
    </script>
@endpush
