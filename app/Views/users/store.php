<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <h3><?= !empty($user) ? 'Edit user' : 'Create user' ?></h3>
    <?php $url = !empty($user['id']) ? "users/store/{$user['id']}" : "users/store" ?>
    <?= form_open($url, ['id' => 'userForm']) ?>
        <div class="mb-3">
            <label for="username">Username</label>
            <input required type="text" name="username" id="username" class="form-control" value="<?= $user['username'] ?? '' ?>">
        </div>
        <div class="mb-3">
            <label for="email">Email</label>
            <input required type="email" name="email" id="email" class="form-control" value="<?= $user['email'] ?? '' ?>">
        </div>
        <div class="mb-3">
            <label for="name">Name</label>
            <input required type="text" name="name" id="name" class="form-control" value="<?= $user['name'] ?? '' ?>">
        </div>
        <div class="mb-3">
            <label for="age">Age</label>
            <input required type="text" name="age" id="age" class="form-control" value="<?= $user['age'] ?? '' ?>">
        </div>
        <div class="mb-3">
            <label for="password">Password</label>
            <input required type="password" name="password" id="password" class="form-control" onkeyup="validatePassword()">
        </div>
        <div class="mb-3">
            <label for="confirmPassword">Confirm password</label>
            <input required type="password" name="confirmPassword" id="confirmPassword" class="form-control" onkeyup="validatePassword()">
            <span id="confirmPasswordSpan"></span>
        </div>
        <input type="submit" class="btn btn-primary" onclick="confirmUserStore(this, 'userForm')">
    <?= form_close() ?>     
    <p><?= anchor('/', 'Home page', ['class' => 'btn btn-primary']) ?></p>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
    function confirmUserStore(el, formId) {
        const form = $(`#${formId}`)
        const url = form.attr('action')
        const formData = form.serialize()

        if (form[0].checkValidity() === false) {
            return;
        }

        this.event.preventDefault()

        if (confirm(`Confirm user store?`)) {
            $.ajax({
                type: "POST",
                url: url,
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                data: formData
            }).always((response, test) => {
                if (response.success && response.message) {
                    alert(`${response.message}`)
                    location.reload();
                } else if (response.responseJSON.message) {
                    alert(`${response.responseJSON.message}`)
                } else {
                    alert(`Storage failed`)
                }
            })
        }
    }

    function validatePassword() {
        const passwd = $('#password')
        const passwdConfirm = $('#confirmPassword')
        const msg = $('#confirmPasswordSpan')

        if (passwd.val() === '' && passwdConfirm.val() === '') {
            msg.html('')
            return
        }

        if (passwd.val().length < 8) {
            var customMsg = 'Password must be at least 8 characters'
            msg.html(customMsg).css('color', 'red')
            passwd[0].setCustomValidity(customMsg)
            return
        }

        if (passwd.val().length < 8) {
            var customMsg = 'Password must be at least 8 characters'
            msg.html(customMsg).css('color', 'red')
            passwd[0].setCustomValidity(customMsg)
            return
        }

        if (passwd.val() === passwdConfirm.val()) {
            msg.html('Matching').css('color', 'green')
            passwd[0].setCustomValidity('')
            return
        }

        var customMsg = 'Passwords do not match'
        msg.html(customMsg).css('color', 'red')
        passwd[0].setCustomValidity(customMsg)
    }
</script>
<?= $this->endSection() ?>