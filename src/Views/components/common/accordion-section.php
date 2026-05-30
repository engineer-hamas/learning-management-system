<div class="card card--glass">
  <div class="section-header" onclick="toggleAccordion('<?= e($accordionId) ?>', 'icon-<?= e($accordionId) ?>')">
    <h3><?= $accordionTitle ?></h3>
    <div style="display: flex; align-items: center; gap: 15px;">
      <?php if (!empty($addAction)): ?>
        <button class="btn btn--primary" onclick="event.stopPropagation(); <?= $addAction ?>">+ Add</button>
      <?php endif; ?>
      <span class="btn btn--icon" id="icon-<?= e($accordionId) ?>">▼</span>
    </div>
  </div>
  <div class="accordion__content" id="<?= e($accordionId) ?>">
    <?= $accordionContent ?>
  </div>
</div>
