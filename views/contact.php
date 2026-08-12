<?php
/** Página Contato — formulário + info + FAQ. */
$c    = $page_content;
$hero  = $c['hero'] ?? [];
include __DIR__ . '/partials/hero.php';

// Processar formulário
$result      = handle_contact_form();
$flash_local = !empty($result) ? $result : ($flash ?? []);
?>
<section class="py-16 px-4">
  <div class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-8">

    <!-- COLUNA ESQUERDA: INFO -->
    <div>
      <?php if (!empty($c['info']['items'])): ?>
      <h2 class="text-2xl font-bold mb-6"><?php echo e($c['info']['title'] ?? 'Contato'); ?></h2>
      <ul class="space-y-4">
        <?php foreach ($c['info']['items'] as $item): ?>
          <li class="flex items-start gap-3">
            <span class="text-xl">
              <?php
                $icons = ['phone' => '📞', 'email' => '✉️', 'location' => '📍', 'clock' => '🕒'];
                echo $icons[$item['icon'] ?? ''] ?? '🔹';
              ?>
            </span>
            <div>
              <div class="text-sm text-slate-500"><?php echo e($item['label']); ?></div>
              <div class="font-medium"><?php echo e($item['value']); ?></div>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
      <?php endif; ?>

      <!-- MAPA -->
      <?php if (feature('google_maps') && config('contact.maps_embed')): ?>
      <div class="mt-6">
        <iframe src="<?php echo e(config('contact.maps_embed')); ?>" width="100%" height="250"
                class="rounded-lg border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
      </div>
      <?php endif; ?>
    </div>

    <!-- COLUNA DIREITA: FORMULÁRIO -->
    <div x-data="{ submitting: false }">
      <?php if (!empty($flash_local['success'])): ?>
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
          ✅ <?php echo e($flash_local['success']); ?>
        </div>
      <?php endif; ?>
      <?php if (!empty($flash_local['error'])): ?>
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
          ❌ <?php echo e($flash_local['error']); ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="/contato" class="bg-white rounded-xl shadow-sm p-6 space-y-4" @submit="submitting = true">
        <?php echo admin_csrf_input(); ?>
        <input type="hidden" name="contact_form" value="1">
        <!-- Honeypot anti-spam -->
        <input type="text" name="website_hp" style="display:none" tabindex="-1" autocomplete="off">

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Nome *</label>
          <input type="text" name="name" required
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">E-mail *</label>
          <input type="email" name="email" required
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Telefone</label>
          <input type="text" name="phone"
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Mensagem *</label>
          <textarea name="message" rows="5" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
        </div>
        <button type="submit"
                class="w-full py-3 rounded-lg bg-[var(--color-primary)] text-white font-semibold hover:opacity-90 transition"
                :disabled="submitting">
          <span x-show="!submitting">Enviar mensagem</span>
          <span x-show="submitting" style="display:none">Enviando...</span>
        </button>
      </form>
    </div>
  </div>
</section>

<!-- FAQ -->
<?php $faq_items = $c['faq'] ?? []; include __DIR__ . '/partials/faq.php'; ?>
