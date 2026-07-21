<div class="dropdown <?= $dropdown['containerClass'] ?? "" ?>" id="<?= $dropdown['id'] ?>" <?= $dropdown['containerAttributes'] ?? "" ?>>
    <button class="btn btn-sm px-1 py-0 <?= $dropdown['toggleClass'] ?>" <?= $dropdown['toggleAttributes'] ?? "" ?> data-bs-toggle="dropdown">
        <?= $dropdown['toggleLabel'] ?>
    </button>
    <ul class="dropdown-menu <?= $dropdown['menuClass'] ?>" <?= $dropdown['menuAttributes'] ?? "" ?>>
        <?php foreach ($dropdown['items'] as $item): ?>
            <li>
                <a class="dropdown-item <?= $item["class"] ?? ""; ?>" href="<?= $item['href'] ?? 'javascript:void(0)' ?>"
                    <?= $item['attributes'] ?? '' ?>>
                    <?php if (!empty($item['icon'])): ?>
                        <i class="<?= $item['icon'] ?>"></i>
                    <?php endif; ?>
                    <?= $item['label'] ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>