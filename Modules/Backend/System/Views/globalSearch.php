<?php if (empty($results)) : ?>
    <div class="dropdown-header p-2">NO USERS FOUND!</div>
<?php else : ?>
    <div class="dropdown-header p-2">FOUND <b><?= count($results) ?></b> USERS</div>
    <?php foreach ($results as $user) : ?>
        <div class="dropdown-item media">
            <div class="media-body">
                <h6 class="media-heading"><?= esc($user['title']) ?></h6>
                <div class="text-muted fs-11px">Email: <?= esc($user['email']) ?></div>
                <div class="text-muted fs-11px">Mobile: <?= esc($user['mobile'] ?? '-') ?></div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>