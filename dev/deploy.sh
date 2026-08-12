#!/usr/bin/env bash
# ============================================================
#  Deploy — cria pacote zip pronto para hospedagem cPanel
#  Uso: cd ~/site-institucional && bash dev/deploy.sh
# ============================================================
set -e

echo "📦 Preparando pacote de deploy..."

WORK_DIR=$(pwd)
TMP_DIR=$(mktemp -d)

# Copiar todos os arquivos do projeto
cp -r * "$TMP_DIR/" 2>/dev/null || true

# Remover pastas/arquivos de desenvolvimento
rm -rf "$TMP_DIR/dev" 2>/dev/null || true
rm -f "$TMP_DIR/tailwind-standalone"* 2>/dev/null || true
rm -rf "$TMP_DIR/node_modules" 2>/dev/null || true

# Incluir arquivos ocultos essenciais
cp -f "$WORK_DIR/.htaccess" "$TMP_DIR/" 2>/dev/null || true
cp -f "$WORK_DIR/.gitignore" "$TMP_DIR/" 2>/dev/null || true

# Criar zip
ZIP_NAME="site-institucional-$(date +%Y%m%d-%H%M%S).zip"
cd "$TMP_DIR" && zip -r "$WORK_DIR/$ZIP_NAME" . >/dev/null 2>&1

# Limpar temporário
rm -rf "$TMP_DIR"

echo "✅ Pacote criado: $ZIP_NAME"
echo "   Tamanho: $(du -h "$WORK_DIR/$ZIP_NAME" | cut -f1)"
echo "   Caminho: $WORK_DIR/$ZIP_NAME"
echo ""
echo "Para subir: use o File Manager do cPanel ou FTP"
