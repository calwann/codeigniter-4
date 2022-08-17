<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="row">
        <?= anchor('/', 'Home', ['class' => 'btn btn-outline-primary mb-3 col-lg-2']) ?>
    </div>
    <table id="myselfTable" class="table">
        <tr>
            <th>Key</th>
            <th>Value</th>
        </tr>
        <?php if (!empty($myself)) : ?>
            <?php foreach ($myself as $key => $value) : ?>
                <tr id="myselfTableRow-<?= $key ?>">
                    <td><?= $key ?></td>
                    <td><?= $value ?></td>
                </tr>
            <?php endforeach ?>
        <?php endif ?>
    </table>
</div>

<?= $this->endSection() ?>