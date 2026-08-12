# Plano de Criação de Modelo de Site Institucional Reutilizável

Este documento descreve o plano completo para criar um template-base de site institucional que pode ser clonado e rapidamente customizado para múltiplos clientes, com stack leve (PHP 8.0+ puro + Tailwind CSS + Alpine.js) e painel administrativo sem banco de dados.

## 📋 Sumário

1. [Estrutura do Projeto](#1-estrutura-do-projeto)
2. [Configuração Inicial](#2-configuração-inicial)
3. [Personalização para Novo Cliente](#3-personalização-para-novo-cliente)
4. [Como Construir o CSS](#4-como-construir-o-css)
5. [Como Testar Localmente](#5-como-testar-localmente)
6. [Como Deployar para Produção](#6-como-deployar-para-produção)
7. [Scripts de Deploy](#7-scripts-de-deploy)

## 1. Estrutura do Projeto

```
site-institucional/
├── .htaccess
├── .gitignore
├── README.md
├── config/
│   └── site.php
├── content/
│   ├── schema/
│   │   ├── home.json
│   │   ├── about.json
│   │   ├── services.json
│   │   └── contact.json
│   ├── settings.json
│   └── (outros JSONs)
├── dev/
│   ├── tailwind.config.js
│   ├── input.css
│   ├── build-css.sh
│   └── tailwind-standalone
├── admin/
│   ├── login.php
│   ├── dashboard.php
│   ├── logout.php
│   ├── edit.php
│   └── settings.php
├── lib/
│   ├── helpers.php
│   ├── json_store.php
│   └── admin.php
├── assets/
│   ├── css/
│   │   └── app.css
│   └── js/
│       ├── alpine.min.js
│       └── app.js
├── views/
│   ├── layout.php
│   ├── partials/
│   │   ├── header.php
│   │   ├── footer.php
│   │   ├── hero.php
│   │   ├── section.php
│   │   ├── card.php
│   │   ├── gallery.php
│   │   ├── faq.php
│   │   ├── cta.php
│   │   └── whatsapp.php
│   ├── home.php
│   ├── about.php
│   ├── services.php
│   └── contact.php
├── dev/
│   └── deploy.sh
└── README.md
```

## 2. Configuração Inicial

### 2.1. Clonar o Template
```bash
git clone https://github.com/seu-usuario/site-institucional.git
cd site-institucional
```

### 2.2. Instalar Dependências (opcional, apenas para build do CSS)
```bash
# Baixar Tailwind CLI (já incluído no dev/)
cd dev
chmod +x tailwind-standalone.exe
```

### 2.3. Estrutura de Diretórios
O projeto já vem com a estrutura completa pronta. Basta copiar para seu diretório de trabalho.

## 3. Personalização para Novo Cliente

### 3.1. Passo a Passo
1. **Criar cópia do template:**
   ```bash
   cp -r site-institucional/ seu-cliente-site
   cd seu-cliente-site
   ```

2. **Editar config/site.php:**
   - Nome da empresa
   - Tagline
   - Logo e favicon
   - Cores do tema (primary, secondary, accent, dark, light)
   - Contatos (telefone, WhatsApp, email, endereço, horários)
   - Redes sociais
   - Menu de navegação
   - SEO (descrição, palavras-chave, OG image)
   - Features (ativar/desativar seções)
   - Plausible Analytics (ativar/desativar e definir domínio)

3. **Editar conteúdo JSON:**
   - `content/home.json` → homepage
   - `content/about.json` → sobre
   - `content/services.json` → serviços
   - `content/contact.json` → contato
   - Edite apenas o conteúdo textual e imagens

4. **Atualizar imagens:**
   - Substitua as imagens em `assets/img/` (logo, favicon, hero, etc.)
   - Atualize referências no JSON se necessário

5. **Recompilar CSS (se mudar cores ou classes):**
   ```bash
   cd dev
   ./build-css.sh
   ```

## 4. Como Construir o CSS

### 4.1. Compilação do Tailwind CSS
O projeto usa o Tailwind CSS com a standalone CLI (sem Node.js necessário):

```bash
cd dev
./build-css.sh
```

Isso compila `dev/input.css` para `assets/css/app.css` com:
- Purge (remover classes não usadas)
- Minificação
- Cache-busting automático

### 4.2. Estrutura do CSS
- `assets/css/app.css`: CSS compilado e minificado (para produção)
- `dev/input.css`: Arquivo de entrada com @tailwind directives
- `dev/tailwind.config.js`: Configuração do Tailwind (cores, purge paths, plugins)

### 4.2. Personalização de Cores
As cores do tema são definidas em `config/site.php` no array `theme`. Elas são injetadas como CSS variables no `<head>`:

```php
--color-primary: #2563eb;
--color-secondary: #0ea5e9;
--color-accent: #f59e0b;
--color-dark: #1e293b;
--color-light: #f8fafc;
```

## 5. Como Testar Localmente

### 5.1. Iniciar Servidor PHP embutido
```bash
php -S localhost:8000
```

Acesse em seu navegador: http://localhost:8000

### 5.2. Testar Responsividade
- Use o modo Developer Tools do seu navegador
- Teste em dispositivos móveis (320px, 768px, 1024px+)
- Verifique se:
  - O menu mobile faz toggle corretamente
  - O botão WhatsApp flutua no canto inferior direito
  - As seções responsivas carregam corretamente

### 5.3. Testar Formulário de Contato
1. Preencha o formulário de contato
2. Verifique se há validação de campos obrigatórios
3. Confirme que o formulário envia e-mail ou usa fallback API
4. Veja as mensagens de sucesso/erro via Alpine.js (sem reload)

### 5.4. Verificar SEO
- Use o Google Lighthouse (no Chrome DevTools)
- Verifique se o score é > 90 em Performance, SEO e Accessibility
- Confirme que o sitemap.xml e robots.txt estão corretos

## 6. Como Deployar para Produção

### 6.1. Preparar para Upload
1. **Excluir pastas de desenvolvimento:**
   ```bash
   rm -rf dev/ tailwind-standalone.exe
   ```

2. **Criar arquivo de deploy:**
   ```bash
   ./dev/deploy.sh
   ```

### 6.2. Deploy em Hospedagem Compartilhada (cPanel)
1. Acesse o cPanel → File Manager
2. Crie uma pasta (ex: `public_html/seusite`)
3. Faça upload de todos os arquivos (exceto `dev/`, `node_modules/`, etc.)
4. Configure o document root para apontar para `public_html` ou a pasta onde está o `index.php`

### 6.3. Configurar Domínio
- Adicione o domínio no cPanel
- Configure o DNS (A record ou CNAME) apontando para o servidor

## 7. Scripts de Deploy

### 7.1. Script de Deploy (dev/deploy.sh)
```bash
#!/bin/bash
# ============================================================
#  Script de Deploy para hospedagem compartilhada
#  Cria um zip pronto para upload em cPanel ou outros hosts
# ============================================================

set -e

echo "📦 Preparando pacote de deploy..."

# Diretório de trabalho
WORK_DIR=$(pwd)
TMP_DIR=$(mktemp -d)

# Limpar conteúdo antigo do TMP_DIR
rm -rf "$TMP_DIR/*"

# Copiar arquivos essenciais
cp -r * "$TMP_DIR/"

# Remover pastas de desenvolvimento
rm -rf "$TMP_DIR/dev" "$TMP_DIR/tailwind-standalone" "$TMP_DIR/node_modules" 2>/dev/null || true

# Incluir .htaccess, .gitignore e sitemap
cp -f .htaccess "$TMP_DIR/"

# Criar zip
ZIP_NAME="site-institucional-$(date +%Y%m%d).zip"
cd "$TMP_DIR" && zip -r "../$ZIP_NAME" . >/dev/null

# Limpar e mostrar resultado
rm -rf "$TMP_DIR"
echo "✅ Pacote criado: $ZIP_NAME"
echo "Tamanho: $(du -h "$ZIP_NAME" | cut -f1)"
echo "Para subir: use o File Manager do cPanel ou FTP"
```

### 7.2. Como usar o script de deploy
```bash
chmod +x dev/deploy.sh
./dev/deploy.sh
```

Isso criará um arquivo `site-institucional-20260811_142000.zip` no diretório raiz, pronto para upload.

## 8. Testar Servidor Localmente

Para testar o ambiente completo:

1. **Iniciar servidor PHP:**
   ```bash
   php -S localhost:8000
   ```

2. **Acessar no navegador:**
   ```
   http://localhost:8000
   ```

3. **Testar funcionalidades:**
   - Navegação entre páginas
   - Responsividade (mobile, tablet, desktop)
   - Formulário de contato
   - Painel admin (acesse /admin/login.php)
   - Painel de configurações (edite settings.json)

## 📌 Considerações Finais

### Vantagens deste Modelo
- **Leve:** Sem dependências Node.js no servidor
- **Portátil:** Funciona em qualquer hospedagem PHP 7.4+
- **Fácil Customização:** Apenas edite `config/site.php` e arquivos JSON
- **SEO Otimizado:** Meta tags, sitemap, robots.txt, structured data
- **Analytics Leve:** Plausible Analytics (1KB, sem consent screen)
- **Seguro:** Headers de segurança, CSRF, validação de formulário

### Limitações Conhecidas
- Não inclui CMS completo (conteúdo editável via JSON)
- Não inclui painel de upload de imagens (requer integração externa)
- Plausible Analytics requer domínio próprio (ou auto-hospedado)

## 📞 Suporte e Contato

Para dúvidas ou suporte, entre em contato com:
- **Desenvolvedor:** Rafael (seu contato)
- **Documentação:** [Link para README.md]

> **Nota:** Este template foi projetado para ser totalmente funcional sem depender de frameworks pesados ou infraestrutura complexa. Ideal para agências que precisam de sites institucionais rápidos e reutilizáveis.