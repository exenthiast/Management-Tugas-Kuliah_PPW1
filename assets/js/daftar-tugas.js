document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const toggleButton = document.getElementById('sidebarCollapse');
            const toggleIcon = toggleButton.querySelector('i');

            if (!sidebar || !mainContent || !toggleButton) {
                console.error('Sidebar, main content, or toggle button not found');
                return;
            }

            toggleButton.addEventListener('click', function (e) {
                e.preventDefault();
                console.log('Toggle button clicked');

                if (window.innerWidth <= 768) {
                    sidebar.classList.toggle('mobile-active');
                    console.log('Mobile mode: mobile-active toggled');
                } else {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('collapsed');
                    console.log('Desktop mode: collapsed toggled');
                }

                toggleIcon.classList.toggle('fa-bars');
                toggleIcon.classList.toggle('fa-times');
            });

            document.addEventListener('click', function (e) {
                if (window.innerWidth <= 768 && sidebar.classList.contains('mobile-active') &&
                    !e.target.closest('#sidebar') && !e.target.closest('#sidebarCollapse')) {
                    sidebar.classList.remove('mobile-active');
                    toggleIcon.classList.remove('fa-times');
                    toggleIcon.classList.add('fa-bars');
                    console.log('Mobile mode: sidebar closed via outside click');
                }
            });

            sidebar.addEventListener('click', function (e) {
                e.stopPropagation();
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('collapsed');
                    if (!sidebar.classList.contains('mobile-active')) {
                        sidebar.classList.remove('mobile-active');
                        toggleIcon.classList.remove('fa-times');
                        toggleIcon.classList.add('fa-bars');
                    }
                } else {
                    sidebar.classList.remove('mobile-active');
                    mainContent.style.marginLeft = sidebar.classList.contains('collapsed') ?
                        'var(--collapsed-sidebar-width)' : 'var(--sidebar-width)';
                }
                console.log('Window resized, new width:', window.innerWidth);
            });
        });

        // Delete functionality
        let deleteId = null;

        function confirmDelete(id) {
            deleteId = id;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (deleteId) {
                // Redirect to delete handler
                window.location.href = 'CRUD/hapus-tugas.php?id=' + deleteId;
            }
        });