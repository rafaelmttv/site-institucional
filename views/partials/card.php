<?php /** Card reutilizável (serviço/feature). @var array $item */ ?>
<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-lg transition">
  <?php if (!empty($item['image'])): ?>
    <img src="<?php echo asset($item['image']); ?>" alt="<?php echo e($item['title']); ?>"
         class="w-full h-48 object-cover" loading="lazy">
  <?php endif; ?>
  <div class="p-5">
    <h3 class="font-bold text-lg mb-2 text-slate-800"><?php echo e($item['title']); ?></h3>
    <?php if (!empty($item['description'])): ?>
      <p class="text-slate-600 text-sm"><?php echo e($item['description']); ?></p>
    <?php endif; ?>
  </div>
</div>
