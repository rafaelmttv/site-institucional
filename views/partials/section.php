<?php /** Seção genérica reutilizável (renderer via JSON). @var array $section */ ?>
<section class="py-16 px-4">
  <div class="max-w-6xl mx-auto">
    <?php if (!empty($section['title'])): ?>
      <h2 class="text-3xl font-bold text-center mb-10 text-slate-800"><?php echo e($section['title']); ?></h2>
    <?php endif; ?>

    <?php $layout = $section['layout'] ?? 'list'; ?>

    <!-- LAYOUT: cards-3 (grid de 3 colunas) -->
    <?php if ($layout === 'cards-3' && !empty($section['items'])): ?>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php foreach ($section['items'] as $item): ?>
          <?php include __DIR__ . '/card.php'; ?>
        <?php endforeach; ?>
      </div>

    <!-- LAYOUT: list (lista vertical) -->
    <?php elseif ($layout === 'list' && !empty($section['items'])): ?>
      <div class="space-y-4 max-w-3xl mx-auto">
        <?php foreach ($section['items'] as $item): ?>
          <div class="flex gap-4 items-start p-4 bg-white rounded-lg shadow-sm">
            <div class="w-10 h-10 rounded-full bg-[var(--color-primary)]/10 flex items-center justify-center flex-shrink-0">
              <span class="text-[var(--color-primary)] font-bold">✓</span>
            </div>
            <div>
              <h3 class="font-semibold text-lg"><?php echo e($item['title']); ?></h3>
              <?php if (!empty($item['description'])): ?>
                <p class="text-slate-600 mt-1"><?php echo e($item['description']); ?></p>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

    <!-- LAYOUT: text (bloco de texto livre) -->
    <?php elseif ($layout === 'text' && !empty($section['content'])): ?>
      <div class="prose prose-lg max-w-3xl mx-auto text-slate-600 leading-relaxed">
        <p><?php echo nl2br(e($section['content'])); ?></p>
      </div>
    <?php endif; ?>
  </div>
</section>
