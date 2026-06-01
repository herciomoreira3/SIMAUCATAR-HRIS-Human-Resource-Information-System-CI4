<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<h1 class="h3 mb-3"><strong>Utilizador</strong></h1>
<div class="row">
    <div class="col-12 col-lg-8 col-xxl-8 d-flex">
        <div class="card flex-fill">
            <div class="card-header">
                <h5 class="card-title mb-0">Lista Utilizador <button class="btn btn-primary btn-sm float-end btnAdd" data-bs-toggle="modal" data-bs-target="#formUserModal">Kria Utilizador Foun</button></h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover my-0">
                        <thead>
                            <tr>
                                <th>Naran</th>
                                <th class="d-none d-xl-table-cell">Naran Utilizador</th>
                                <th>Papel</th>
                                <th>Data Kria</th>
                                <th>Asaun</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($Users as $users) : ?>
                                <tr>
                                    <td><?= $users['fullname']; ?></td>
                                    <td class="d-none d-md-table-cell"><?= $users['username']; ?></td>
                                    <td><span class="badge bg-success"><?= esc($users['role_name'] ?? $users['role'] ?? '-'); ?></span></td>
                                    <td><?= $users['created_at']; ?></td>
                                    <td>
                                        <button class="btn btn-info btn-sm btnEdit" data-bs-toggle="modal" data-bs-target="#formUserModal" data-id="<?= $users['userID']; ?>" data-fullname="<?= $users['fullname']; ?>" data-username="<?= $users['username']; ?>" data-role="<?= $users['role']; ?>">Atualiza</button>

                                        <?php if ($users['username'] != session()->get('username')) : ?>
                                            <form action="<?= base_url('users/delete-user/' . $users['userID']); ?>" method="post" class="d-inline">
                    <?= csrf_field() ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button class="btn btn-danger btn-sm" onclick="return confirm('Ita boot hakarak hamos <?= $users['username']; ?> ?')">Hamos</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4 col-xxl-4 d-flex">
        <div class="card flex-fill">
            <div class="card-header">
                <h5 class="card-title mb-0">Papel Utilizador <button class="btn btn-primary btn-sm float-end btnAddRole" data-bs-toggle="modal" data-bs-target="#formRoleModal">Kria Papel Foun</button></h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover my-0">
                        <thead>
                            <tr>
                                <th>Papel</th>
                                <th colspan="2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($UserRole as $userRole) : ?>
                                <tr>
                                    <td><?= $userRole['role_name']; ?></td>
                                    <td><a href="<?= base_url('users/role-access?role=' . $userRole['id']); ?>"> <span class="badge bg-primary">Asesu Menu</span></a></td>
                                    <td>
                                        <button class="btn btn-info btn-sm btnEditRole" data-bs-toggle="modal" data-bs-target="#formRoleModal" data-id="<?= $userRole['id']; ?>" data-role="<?= $userRole['role_name']; ?>">Atualiza</button>
                                        <form action="<?= base_url('users/delete-role/' . $userRole['id']); ?>" method="post" class="d-inline">
                    <?= csrf_field() ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                Hamos
                                            </button>
                                        </form>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('widgets/users/user_form_modal'); ?>
<?= $this->include('widgets/users/role_form_modal'); ?>
<?= $this->endSection(); ?>

<?= $this->section('javascript'); ?>
<script>
    $(document).ready(function() {
        $(".btnAdd").click(function() {
            $('#formUserModalLabel').html('Kria Utilizador Foun');
            $('.modal-footer button[type=submit]').html('Rai Utilizador');
            $('#userID').val('');
            $('#inputFullname').val('');
            $('#inputUsername').val('');
            $('#inputRole').val('');
        });
        $(".btnEdit").click(function() {
            const userId = $(this).data('id');
            const fullname = $(this).data('fullname');
            const username = $(this).data('username');
            const role = $(this).data('role');
            $('#modalTitle').html('Atualiza Dadus Utilizador');
            $('.modal-footer button[type=submit]').html('Atualiza Utilizador');
            $('.modal-content form').attr('action', '<?= base_url('users/update-user') ?>');
            $('#userID').val(userId);
            $('#inputFullname').val(fullname);
            $('#inputUsername').val(username);
            $('#inputUsername').attr('readonly', true);
            $('#inputPassword').attr('required', false);
            $('#inputRole').val(role);
        });

        $(".btnAddRole").click(function() {
            $('#formUserModalLabel').html('Kria Papel Foun');
            $('.modal-content form').attr('action', '<?= base_url('users/create-role') ?>');
            $('.modal-footer button[type=submit]').html('Rai Papel');
            $('#roleID').val('');
            $('#inputRoleName').val('');
        });
        $(".btnEditRole").click(function() {
            const roleID = $(this).data('id');
            const inputRoleName = $(this).data('role');
            $('#modalTitle').html('Atualiza Dadus Papel');
            $('.modal-footer button[type=submit]').html('Atualiza Papel');
            $('.modal-content form').attr('action', '<?= base_url('users/update-role') ?>');
            $('#roleID').val(roleID);
            $('#inputRoleName').val(inputRoleName);
        });
    });
</script>
<?= $this->endSection(); ?>
