    <!-- Toast Notification -->
    <div id="toast-container" class="fixed top-6 right-6 z-[1100] space-y-3">
        <?php $flash = getFlashMessage(); if ($flash): ?>
            <div class="toast flex items-center gap-3 px-6 py-4 rounded-xl shadow-lg text-white font-interface font-medium text-sm
                <?php echo $flash['type'] === 'success' ? 'bg-hemo-success' : ($flash['type'] === 'error' ? 'bg-hemo-warning' : 'bg-hemo-info'); ?>"
                 style="animation: slideIn 0.3s ease, fadeOut 0.5s ease 4s forwards;">
                <i class="fas <?php echo $flash['type'] === 'success' ? 'fa-check-circle' : ($flash['type'] === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'); ?>"></i>
                <?php echo sanitize($flash['message']); ?>
            </div>
        <?php endif; ?>
    </div>

    <style>
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes fadeOut {
            to { opacity: 0; transform: translateY(-10px); }
        }
    </style>

    <script>
        // Mobile sidebar toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            if (sidebar) {
                sidebar.classList.toggle('-translate-x-full');
                if (overlay) overlay.classList.toggle('hidden');
            }
        }

        // Auto-dismiss toast after 5s
        setTimeout(() => {
            const toasts = document.querySelectorAll('.toast');
            toasts.forEach(t => t.remove());
        }, 5000);
    </script>
</body>
</html>
