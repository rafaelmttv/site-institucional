<?php /** Navbar responsiva com toggle mobile (Alpine). */ ?>
<header x-data="{ open: false, scrolled: false }"
        @scroll.window="scrolled = window.scrollY > 20"
        :class="scrolled ? 'bg-white shadow-md' : 'bg-white/90 backdrop-blur'"
        class="sticky top-0 z-50 transition-all duration-300">
  <nav class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="flex items-center justify-between h-16">

      <!-- LOGO -->
      <a href="/" class="flex items-center gap-2">
        <?php if ($logo = config('logo')): ?>
          <img src="<?php echo asset($logo); ?>" alt="<?php echo e(config('name')); ?>" class="h-8 w-auto">
        <?php endif; ?>
        <span class="font-bold text-lg text-[var(--color-primary)]"><?php echo e(config('name')); ?></span>
      </a>

      <!-- NAV DESKTOP -->
      <div class="hidden md:flex items-center gap-1">
        <?php foreach (config('nav', []) as $item): ?>
          <a href="<?php echo e($item['url']); ?>"
             class="px-3 py-2 rounded-lg text-sm font-medium hover:bg-[var(--color-primary)] hover:text-white transition">
            <?php echo e($item['label']); ?>
          </a>
        <?php endforeach; ?>
        <a href="/contato"
           class="ml-2 px-4 py-2 rounded-lg text-sm font-semibold bg-[var(--color-primary)] text-white hover:opacity-90 transition">
          Fale conosco
        </a>
      </div>

      <!-- TOGGLE MOBILE -->
      <button @click="open = !open" class="md:hidden p-2 rounded-lg hover:bg-slate-100" aria-label="Menu">
        <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
        <svg x-show="open" style="display:none" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <!-- NAV MOBILE -->
    <div x-show="open" x-transition style="display:none" class="md:hidden pb-4">
      <?php foreach (config('nav', []) as $item): ?>
        <a href="<?php echo e($item['url']); ?>" @click="open = false"
           class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">
          <?php echo e($item['label']); ?>
        </a>
      <?php endforeach; ?>
      <a href="/contato" @click="open = false"
         class="mt-2 block px-3 py-2 rounded-lg text-sm font-semibold text-center bg-[var(--color-primary)] text-white">
        Fale conosco
      </a>
    </div>
  </nav>
</header>
