</main>

<!-- Confirm Delete Modal (shared) -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Confirm Delete</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">Are you sure? This cannot be undone.</div>
            <div class="modal-footer">
                <form id="deleteForm" method="GET"><input type="hidden" name="id" id="deleteId">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Sidebar toggle
    document.getElementById('toggleSidebar').addEventListener('click', () => {
        const sb = document.getElementById('sidebar');
        sb.classList.toggle('collapsed');
        const icon = document.querySelector('#toggleSidebar i');
        icon.classList.toggle('bi-list'); icon.classList.toggle('bi-x');
    });

    // Delete buttons (shared)
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            const url = btn.dataset.deleteUrl;
            const id  = btn.dataset.id;
            document.getElementById('deleteForm').action = url;
            document.getElementById('deleteId').value = id;
            new bootstrap.Modal(document.getElementById('confirmDeleteModal')).show();
        });
    });
</script>
</body>
</html>