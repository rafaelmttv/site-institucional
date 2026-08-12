#!/usr/bin/env bash
# ============================================================
#  Build CSS — compila Tailwind para assets/css/app.css
#  Uso: cd dev/ && bash build-css.sh  (ou ./build-css.sh)
# ============================================================
set -e

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"

echo "🔨 Compilando Tailwind CSS..."

# Detecta binário disponível (procura .exe primeiro — funciona em Windows nativo E Git Bash/MINGW)
if [ -f "$SCRIPT_DIR/tailwind-standalone.exe" ]; then
  BINARY="$SCRIPT_DIR/tailwind-standalone.exe"
elif [ -f "$SCRIPT_DIR/tailwind-standalone-linux" ]; then
  BINARY="$SCRIPT_DIR/tailwind-standalone-linux"
elif [ -f "$SCRIPT_DIR/tailwind-standalone-macos" ]; then
  BINARY="$SCRIPT_DIR/tailwind-standalone-macos"
else
  echo "❌ Nenhum binário do Tailwind encontrado em: $SCRIPT_DIR"
  echo "   Baixe em: https://github.com/tailwindlabs/tailwindcss/releases"
  echo "   Coloque como: tailwind-standalone.exe (Windows) ou tailwind-standalone-linux (Linux/WSL)"
  exit 1
fi

# Converter paths MSYS (/c/...) para formato Windows (C:\...) se o binário for .exe
# O .exe não entende paths POSIX do Git Bash
to_win_path() {
  if [[ "$1" == /* ]]; then
    # /c/Users/... -> C:\Users\...
    local drive=$(echo "$1" | sed 's|^/\([a-zA-Z]\)/|\1:\\|' | tr '/' '\\')
    echo "$drive"
  else
    echo "$1"
  fi
}

if [[ "$BINARY" == *.exe ]]; then
  INPUT_PATH=$(to_win_path "$SCRIPT_DIR/input.css")
  CONFIG_PATH=$(to_win_path "$SCRIPT_DIR/tailwind.config.js")
  OUTPUT_PATH=$(to_win_path "$PROJECT_DIR/assets/css/app.css")
else
  INPUT_PATH="$SCRIPT_DIR/input.css"
  CONFIG_PATH="$SCRIPT_DIR/tailwind.config.js"
  OUTPUT_PATH="$PROJECT_DIR/assets/css/app.css"
fi

"$BINARY" \
  -i "$INPUT_PATH" \
  -c "$CONFIG_PATH" \
  -o "$OUTPUT_PATH" \
  --minify

echo "✅ CSS compilado: assets/css/app.css ($(wc -c < "$PROJECT_DIR/assets/css/app.css") bytes)"
