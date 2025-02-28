<?= $this->extend('layout/layout') ?>

<?= $this->section('content') ?>
<div class="text-center">
    <h1 class="display-4">Welcome to BeatBattle Royale! 🎶</h1>
    <p class="lead">Test your music knowledge and compete against players worldwide.</p>
    <a href="<?= base_url('/game_lobby') ?>" class="btn btn-primary btn-lg">🎮 Start Game</a>
</div>
<?= $this->endSection() ?>
