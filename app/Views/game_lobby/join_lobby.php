<?= $this->extend('layout/layout') ?>

<?= $this->section('content') ?>
<div class="container text-center">
    <h2>Join a Game Lobby</h2>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <form action="<?= base_url('/join_lobby') ?>" method="POST" class="mt-4">
        <div class="mb-3">
            <label class="form-label">Enter Game Code:</label>
            <input type="text" name="game_code" class="form-control text-uppercase text-center" required>
        </div>

        <?php if (!session()->has('user_id')): ?>
            <div class="mb-3">
                <label class="form-label">Enter a Name (Guests Only):</label>
                <input type="text" name="guest_name" class="form-control text-center" required>
            </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary">Join Game</button>
    </form>

    <p class="mt-4">or</p>

    <form action="<?= base_url('/create_lobby') ?>" method="POST">
        <button type="submit" class="btn btn-success">Create a New Lobby</button>
    </form>
</div>
<?= $this->endSection() ?>
