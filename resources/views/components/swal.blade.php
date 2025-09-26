@once
    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    (function () {
        // Helper: tampilkan toast
        function showToast({ icon = 'info', title = '', timer = 2200 } = {}) {
            Swal.fire({
                toast: true,
                position: 'top',
                icon,
                title,
                showConfirmButton: false,
                timer,
                timerProgressBar: true
            });
        }

        // 1) Session flash -> toast (dipanggil on DOM ready)
        document.addEventListener('DOMContentLoaded', () => {
            @if (session('message'))
                showToast({ icon: 'success', title: @json(session('message')), timer: 2500 });
            @endif
            @if (session('error'))
                showToast({ icon: 'error', title: @json(session('error')), timer: 3000 });
            @endif
            @if (session('warning'))
                showToast({ icon: 'warning', title: @json(session('warning')), timer: 3000 });
            @endif
            @if (session('info'))
                showToast({ icon: 'info', title: @json(session('info')), timer: 2500 });
            @endif
        });

        // 2) Livewire -> toast generik
        window.addEventListener('toast', (e) => {
            const { type = 'info', message = '', timer = 2200 } = e.detail || {};
            showToast({ icon: type, title: message, timer });
        });

        // 3) Livewire -> confirm delete
        window.addEventListener('confirm-delete', (e) => {
            const { id, title, text, confirmText, cancelText } = (e.detail || {});
            Swal.fire({
                title: title || 'Hapus data ini?',
                text:  text || 'Aksi ini tidak dapat dibatalkan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: confirmText || 'Ya, hapus',
                cancelButtonText:  cancelText || 'Batal'
            }).then(result => {
                if (result.isConfirmed) {
                    // Trigger event balik ke komponen Livewire untuk eksekusi hapus
                    if (window.Livewire && typeof Livewire.dispatch === 'function') {
                        Livewire.dispatch('perform-delete', { id });
                    }
                }
            });
        });
    })();
    </script>
@endonce
