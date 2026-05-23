<div class="accordion__item">
  <div class="accordion__header" onclick="toggleAccordion('<?= e($accordionId) ?>', 'icon-<?= e($accordionId) ?>')">
    <h3><?= $accordionTitle ?></h3>
    <div style="display: flex; align-items: center; gap: 15px;">
      <?php if (!empty($addAction)): ?>
        <button class="add-btn" onclick="event.stopPropagation(); <?= $addAction ?>">+ Add</button>
      <?php endif; ?>
      <span class="accordion__icon" id="icon-<?= e($accordionId) ?>">▼</span>
    </div>
  </div>
  <div class="accordion__content" id="<?= e($accordionId) ?>">
    <?= $accordionContent ?>
  </div>
</div>
