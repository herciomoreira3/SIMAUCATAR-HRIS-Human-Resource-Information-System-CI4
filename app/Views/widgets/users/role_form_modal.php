<div class="modal" id="formRoleModal" tabindex="-1" aria-labelledby="formUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formUserModalLabel">Kria Papel Foun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Taka"></button>
            </div>
            <form action="<?= base_url('users/create-role'); ?> " method="post">
                    <?= csrf_field() ?>
                <div class="modal-body">
                    <input type="hidden" name="roleID" id="roleID">
                    <div class="mb-3">
                        <label for="inputRoleName" class="form-label">Aumenta Papel</label>
                        <input type="text" class="form-control" id="inputRoleName" name="inputRoleName" placeholder="Naran Papel">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Rai Papel</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Taka</button>
                </div>
            </form>

        </div>
    </div>
</div>
