<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="container mt-5">
    <?= anchor('/users/create', 'Create new user', ['class' => 'btn btn-outline-primary mb-3']) ?>
    <?= anchor('/users/myself', 'Myself', ['class' => 'btn btn-outline-primary mb-3']) ?>
    <?= anchor('/login', 'Login', ['class' => 'btn btn-outline-success mb-3']) ?>
    <?= anchor('/logout', 'Logout', ['class' => 'btn btn-outline-danger mb-3']) ?>
    <table id="userTable" class="table">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Name</th>
            <th>Age</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($users as $user) : ?>
            <tr id="userTableRow-<?= $user['id'] ?>">
                <td><?= $user['id'] ?></td>
                <td><?= $user['username'] ?></td>
                <td><?= $user['email'] ?></td>
                <td><?= $user['name'] ?></td>
                <td><?= $user['age'] ?></td>
                <td><?= $user['status'] ?></td>
                <td>
                    <?= anchor("users/edit/{$user['id']}", 'Edit') ?>
                    <span>|</span>
                    <?= anchor("users/delete/{$user['id']}", 'Delete', ['onclick' => "confirmUserDelete(this, {$user['id']})"]) ?>
                </td>
            </tr>
        <?php endforeach ?>
    </table>
    <div>
        <?= $pager->links() ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
    function confirmUserDelete(el, id) {
        this.event.preventDefault()
        const url = el.href

        if (confirm(`Confirm user deletion?`)) {
            $.ajax({
                type: "POST",
                url: url,
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                data: {}
            }).always((response) => {
                if (response.success && response.message) {
                    alert(`${response.message}`)
                    $(`#userTableRow-${id}`).hide()
                } else if (response.responseJSON.message) {
                    alert(`${response.responseJSON.message}`)
                } else {
                    alert(`Deletion failed`)
                }
            })
        }
    }
</script>
<?= $this->endSection() ?>