<?php /** Rodapé — contatos, redes sociais, horários. */ ?>
<footer class="bg-slate-800 text-slate-300 mt-16">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

      <!-- COLUNA 1: Empresa -->
      <div>
        <h3 class="text-white font-bold text-lg mb-3"><?php echo e(config('name')); ?></h3>
        <p class="text-sm text-slate-400"><?php echo e(config('tagline')); ?></p>
      </div>

      <!-- COLUNA 2: Contato -->
      <div>
        <h4 class="text-white font-semibold mb-3">Contato</h4>
        <ul class="space-y-2 text-sm">
          <?php if ($p = config('contact.phone')): ?>
            <li><a href="tel:<?php echo e($p); ?>" class="hover:text-white">📞 <?php echo e($p); ?></a></li>
          <?php endif; ?>
          <?php if ($em = config('contact.email')): ?>
            <li><a href="mailto:<?php echo e($em); ?>" class="hover:text-white">✉ <?php echo e($em); ?></a></li>
          <?php endif; ?>
          <?php if ($a = config('contact.address')): ?>
            <li>📍 <?php echo e($a); ?></li>
          <?php endif; ?>
          <?php if ($h = config('contact.hours')): ?>
            <li>🕒 <?php echo e($h); ?></li>
          <?php endif; ?>
        </ul>
      </div>

      <!-- COLUNA 3: Redes sociais -->
      <div>
        <h4 class="text-white font-semibold mb-3">Redes Sociais</h4>
        <div class="flex gap-3">
          <?php foreach (config('social', []) as $net => $url): ?>
            <?php if ($url): ?>
              <a href="<?php echo e($url); ?>" target="_blank" rel="noopener"
                 class="w-10 h-10 rounded-full bg-white/10 hover:bg-[var(--color-primary)] flex items-center justify-center transition">
                <span class="text-sm uppercase"><?php echo e(substr($net, 0, 2)); ?></span>
              </a>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <div class="border-t border-white/10 mt-8 pt-6 text-center text-sm text-slate-400">
      <p>&copy; <?php echo date('Y'); ?> <?php echo e(config('name')); ?>. Todos os direitos reservados.</p>
    </div>
  </div>
</footer>
