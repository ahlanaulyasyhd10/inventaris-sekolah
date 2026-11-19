/* JS aplikasi: DataTables, filter kolom, konfirmasi (SweetAlert2), dan kolom foto */

// Tabler menyimpan objek Bootstrap di window.tabler.bootstrap, bukan window.bootstrap.
window.bootstrap = window.bootstrap || (window.tabler && window.tabler.bootstrap);

(function ($) {
    'use strict';

    var idLanguage = {
        search: 'Cari:',
        lengthMenu: 'Tampilkan _MENU_ data',
        info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
        infoEmpty: 'Tidak ada data',
        infoFiltered: '(difilter dari _MAX_ data)',
        zeroRecords: 'Data tidak ditemukan',
        emptyTable: 'Belum ada data',
        paginate: { first: 'Pertama', last: 'Terakhir', next: 'Berikutnya', previous: 'Sebelumnya' }
    };

    // Mode terang/gelap (disimpan di localStorage)
    function applyThemeIcon() {
        var dark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        $('#theme-icon').toggleClass('ti-moon', !dark).toggleClass('ti-sun', dark);
    }

    var tables = {};

    $(function () {
        applyThemeIcon();
        $('#theme-toggle').on('click', function (e) {
            e.preventDefault();
            var next = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-bs-theme', next);
            try { localStorage.setItem('inventa-theme', next); } catch (err) {}
            applyThemeIcon();
        });

        $('table.datatable').each(function () {
            var table = $(this).DataTable({
                language: idLanguage,
                pageLength: 10,
                order: [],
                autoWidth: false,
                columnDefs: [{ targets: 'no-sort', orderable: false, searchable: false }]
            });
            if (this.id) { tables[this.id] = table; }
            $(this).closest('.dt-container').find('.dt-search input').attr('placeholder', 'Ketik untuk mencari...');
        });

        // Filter per kolom: <select data-table-filter="#tableId" data-column="3">
        $('select[data-table-filter]').on('change', function () {
            var table = tables[String($(this).data('table-filter')).replace('#', '')];
            if (!table) { return; }
            table.column($(this).data('column')).search(this.value, false, true).draw();
        });

        // Fokus otomatis ke field pertama saat modal dibuka
        $(document).on('shown.bs.modal', '.modal', function () {
            $(this).find('input:not([type=hidden]), select, textarea').first().trigger('focus');
        });

        // ---------- Tanda error merah hilang begitu kolom diisi ----------
        $(document).on('input change', '.is-invalid', function () {
            var filled;
            if ($(this).is(':checkbox, :radio')) { filled = this.checked; }
            else if (this.type === 'file') { filled = !!(this.files && this.files.length); }
            else { filled = $.trim($(this).val()) !== ''; }
            if (filled) { $(this).removeClass('is-invalid'); }
        });

        // ---------- Pop-up konfirmasi (SweetAlert2) ----------
        // Dipakai untuk semua aksi hapus dan aksi penting lainnya.
        function confirmThen(opts, onConfirm) {
            if (!window.Swal) {
                if (window.confirm(opts.text || 'Lanjutkan?')) { onConfirm(); }
                return;
            }
            Swal.fire({
                title: opts.title || 'Apakah kamu yakin?',
                text: opts.text || '',
                icon: opts.icon || 'warning',
                showCancelButton: true,
                confirmButtonText: opts.confirmText || 'Ya, lanjutkan',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true,
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-' + (opts.variant || 'danger') + ' ms-2',
                    cancelButton: 'btn'
                }
            }).then(function (result) {
                if (result.isConfirmed) { onConfirm(); }
            });
        }

        // Form dengan atribut data-confirm="pesan" (opsional: data-confirm-button, data-confirm-variant, data-confirm-icon)
        $(document).on('submit', 'form[data-confirm]', function (e) {
            var form = this;
            if (form.dataset.confirmed === '1') { return; }
            e.preventDefault();
            confirmThen({
                text: form.getAttribute('data-confirm'),
                confirmText: form.getAttribute('data-confirm-button') || 'Ya, lanjutkan',
                variant: form.getAttribute('data-confirm-variant') || 'danger',
                icon: form.getAttribute('data-confirm-icon') || 'warning'
            }, function () {
                form.dataset.confirmed = '1';
                form.submit();
            });
        });

        // Tombol di luar form: data-confirm-submit="#idForm" data-confirm-text="pesan"
        $(document).on('click', '[data-confirm-submit]', function () {
            var target = document.querySelector(this.getAttribute('data-confirm-submit'));
            if (!target) { return; }
            confirmThen({
                text: this.getAttribute('data-confirm-text'),
                confirmText: this.getAttribute('data-confirm-button') || 'Ya, hapus'
            }, function () { target.submit(); });
        });

        // ---------- Kolom foto: pilih berkas -> pratinjau + tombol "Ubah" ----------
        $('.photo-field').each(function () {
            var $f = $(this);
            var $input = $f.find('.pf-input');
            var $img = $f.find('.pf-preview');
            $f.find('.pf-change').on('click', function () { $input[0].click(); });
            $input.on('change', function () {
                var file = this.files && this.files[0];
                if (!file) { return; }
                $img.attr('src', URL.createObjectURL(file)).removeClass('d-none');
                $f.find('.pf-placeholder, .pf-hint, .pf-delete, .pf-note').addClass('d-none');
                $input.addClass('d-none');
                $f.find('.pf-actions, .pf-new').removeClass('d-none');
            });
        });
    });
})(jQuery);
