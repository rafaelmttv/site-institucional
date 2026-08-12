<?php /** Accordion FAQ (Alpine). @var array $faq_items */ ?>
<?php $faq_items = $faq_items ?? $page_content['faq'] ?? []; ?>
<?php if (!empty($faq_items) && feature('faq')): ?>
<section class="py-16 px-4">
  <div class="max-w-3xl mx-auto" x-data="{ open: null }">
    <h2 class="text-3xl font-bold text-center mb-10">Perguntas Frequentes</h2>
    <div class="space-y-3">
      <?php foreach ($faq_items as $i => $item): ?>
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
          <button @click="open === <?php echo $i; ?> ? open = null : open = <?php echo $i; ?>"
                  class="w-full px-5 py-4 text-left font-semibold flex items-center justify-between hover:bg-slate-50">
            <span><?php echo e($item['question']); ?></span>
            <span x-show="open !== <?php echo $i; ?>">+</span>
            <span x-show="open === <?php echo $i; ?>" style="display:none">−</span>
          </button>
          <div x-show="open === <?php echo $i; ?>" x-collapse style="display:none">
            <p class="px-5 pb-4 text-slate-600"><?php echo e($item['answer']); ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
