<?php /** Seção de destaque (banner principal). @var array $hero */ ?>
<?php $hero = $hero ?? $page_content['hero'] ?? []; ?>
<?php if (!empty($hero)): ?>
<section class="relative bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] text-white py-20 px-4">
  <div class="max-w-5xl mx-auto text-center">
    <?php if (!empty($hero['title'])): ?>
      <h1 class="text-4xl md:text-5xl font-extrabold mb-4"><?php echo e($hero['title']); ?></h1>
    <?php endif; ?>
    <?php if (!empty($hero['subtitle'])): ?>
      <p class="text-lg md:text-xl text-white/90 max-w-2xl mx-auto mb-8"><?php echo e($hero['subtitle']); ?></p>
    <?php endif; ?>
    <?php if (!empty($hero['cta_text'])): ?>
      <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="<?php echo e($hero['cta_url'] ?? '/contato'); ?>"
           class="px-6 py-3 rounded-lg bg-white text-[var(--color-primary)] font-semibold hover:bg-slate-100 transition">
          <?php echo e($hero['cta_text']); ?>
        </a>
        <?php if (!empty($hero['secondary_cta_text'])): ?>
          <a href="<?php echo e($hero['secondary_cta_url'] ?? '/servicos'); ?>"
             class="px-6 py-3 rounded-lg border-2 border-white/50 text-white font-semibold hover:bg-white/10 transition">
            <?php echo e($hero['secondary_cta_text']); ?>
          </a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>
