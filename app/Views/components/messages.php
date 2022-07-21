<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="alert alert-info">
        <p><?= $message ?></p>
        <p><?= anchor('/', 'Home page') ?></p>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<?= $this->endSection() ?>