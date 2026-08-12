<?php
/** Homepage — hero + features + seções + CTA. */
$c = $page_content;
?>
<?php include __DIR__ . '/partials/hero.php'; ?>

<!-- FEATURES -->
<?php if (!empty($c['features'])): ?>
<section class="py-16 px-4">
  <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
    <?php foreach ($c['features'] as $f): ?>
      <div class="text-center p-6 bg-white rounded-xl shadow-sm">
        <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-[var(--color-primary)]/10 flex items-center justify-center text-2xl">
          <?php
            $icons = ['shield' => '🛡️', 'clock' => '⏰', 'users' => '👥', 'star' => '⭐', 'check' => '✅'];
            echo $icons[$f['icon'] ?? ''] ?? '🔹';
          ?>
        </div>
        <h3 class="font-bold text-lg mb-2"><?php echo e($f['title']); ?></h3>
        <p class="text-slate-600 text-sm"><?php echo e($f['description']); ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- SEÇÕES DINÂMICAS -->
<?php foreach ($c['sections'] ?? [] as $section): ?>
  <?php include __DIR__ . '/partials/section.php'; ?>
<?php endforeach; ?>

<!-- CTA -->
<?php include __DIR__ . '/partials/cta.php'; ?>
