<div class="modal" id="formUserModal" tabindex="-1" aria-labelledby="formUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formUserModalLabel">Kria Utilizador Foun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Taka"></button>
            </div>
            <form action="<?= base_url('users/create-user'); ?>" method="POST">
                    <?= csrf_field() ?>
                <div class="modal-body">
                    <input type="hidden" name="userID" id="userID">
                    <div class="mb-3">
                        <label for="inputFullname" class="col-form-label">Naran Kompletu:</label>
                        <input type="text" class="form-control" name="inputFullname" id="inputFullname" required>
                    </div>
                    <div class="mb-3">
                        <label for="inputUsername" class="col-form-label">Naran Utilizador:</label>
                        <input type="text" class="form-control" name="inputUsername" id="inputUsername" required>
                    </div>
                    <div class="mb-3">
                        <label for="inputPassword" class="col-form-label">Senha:</label>
                        <input type="password" class="form-control" name="inputPassword" id="inputPassword" required>
                    </div>
                    <div class="mb-3">
                        <label for="inputRole" class="col-form-label">Papel:</label>
                        <select name="inputRole" id="inputRole" class="form-control" required>
                            <option value="">-- Hili Papel Utilizador --</option>
                            <?php foreach ($UserRole as $userRole) : ?>
                                <option value="<?= $userRole['id']; ?>"><?= $userRole['role_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Rai</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Taka</button>
                </div>
            </form>
        </div>
    </div>
</div>
