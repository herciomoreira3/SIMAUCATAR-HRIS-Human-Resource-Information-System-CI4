<?php if (session()->getFlashdata('success')) : ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Susesu!',
            text: '<?= session()->getFlashdata('success'); ?>',
            timer: 3000,
            showConfirmButton: false
        })
    </script>
<?php endif ?>

<?php if (session()->getFlashdata('error')) : ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: '<?= session()->getFlashdata('error'); ?>'
        })
    </script>
<?php endif ?>

<?php if (session()->getFlashdata('notif_success')) : ?>
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <div class="alert-icon">
            <i class="align-middle" data-feather="check-circle"></i>
        </div>
        <div class="alert-message">
            <?= session()->getFlashdata('notif_success'); ?>
        </div>
    </div>
<?php endif ?>

<script>
    $(document).ready(function() {
        $('.datatable').each(function() {
            var orderAttr = $(this).attr('data-order');
            var defaultOrder = [[0, 'desc']]; 
            if (orderAttr) {
                try {
                    defaultOrder = JSON.parse(orderAttr);
                } catch (e) {
                    console.error('Invalid format', orderAttr);
                }
            }
            $(this).DataTable({
                "order": defaultOrder,
                "language": {
                    "search": "Buka:",
                    "lengthMenu": "Hatudu _MENU_ dadus",
                    "info": "Hatudu _START_ to'o _END_ husi _TOTAL_ dadus",
                    "paginate": {
                        "next": "Oituan",
                        "previous": "Atras"
                    }
                }
            });
        });
    });
</script>