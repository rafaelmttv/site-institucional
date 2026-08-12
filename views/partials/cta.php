<?php /** Call-to-action bar. @var array $cta */ ?>
<?php $cta = $cta ?? $page_content['cta'] ?? []; ?>
<?php if (!empty($cta['title'])): ?>
<section class="py-16 px-4 bg-[var(--color-primary)] text-white">
  <div class="max-w-3xl mx-auto text-center">
    <h2 class="text-3xl font-bold mb-3"><?php echo e($cta['title']); ?></h2>
    <?php if (!empty($cta['text'])): ?>
      <p class="text-lg text-white/90 mb-6"><?php echo e($cta['text']); ?></p>
    <?php endif; ?>
    <?php if (!empty($cta['button_text'])): ?>
      <a href="<?php echo e($cta['button_url'] ?? '/contato'); ?>"
         class="inline-block px-6 py-3 rounded-lg bg-white text-[var(--color-primary)] font-semibold hover:bg-slate-100 transition">
        <?php echo e($cta['button_text']); ?>
      </a>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>
