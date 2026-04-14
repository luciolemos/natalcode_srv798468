#!/usr/bin/env bash
set -euo pipefail

# Release helper: validate and prepare/perform merge from DEV_BRANCH to RELEASE_BRANCH.
# Default mode is DRY-RUN (runs checks and prints next commands).
#
# Usage:
#   scripts/release-main.sh
#   scripts/release-main.sh --apply
#
# Optional env vars:
#   DEV_BRANCH=natalcode RELEASE_BRANCH=main RUN_VISUAL=1 scripts/release-main.sh --apply

DEV_BRANCH="${DEV_BRANCH:-natalcode}"
RELEASE_BRANCH="${RELEASE_BRANCH:-main}"
RUN_VISUAL="${RUN_VISUAL:-1}"
APPLY_MODE=0

if [[ "${1:-}" == "--apply" ]]; then
  APPLY_MODE=1
elif [[ "${1:-}" != "" ]]; then
  echo "Uso: $0 [--apply]"
  exit 1
fi

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"
cd "${REPO_DIR}"

CURRENT_BRANCH="$(git rev-parse --abbrev-ref HEAD)"

if ! git diff --quiet || ! git diff --cached --quiet; then
  echo "Erro: existem alterações locais não commitadas."
  echo "Faça commit/stash antes de rodar o release."
  exit 1
fi

echo "==> Sincronizando branches..."
git fetch origin
git checkout "${DEV_BRANCH}"
git pull --rebase origin "${DEV_BRANCH}"

echo "==> Rodando checks de qualidade..."
XDEBUG_MODE=off vendor/bin/phpunit --configuration phpunit.xml
XDEBUG_MODE=off vendor/bin/phpstan analyse --configuration phpstan.neon.dist --no-progress
XDEBUG_MODE=off vendor/bin/phpcs --standard=phpcs.xml --extensions=php -n src app tests

if [[ "${RUN_VISUAL}" == "1" ]]; then
  env -u NO_COLOR XDEBUG_MODE=off npm run test:visual
else
  echo "Aviso: RUN_VISUAL=0, pulando testes visuais."
fi

if [[ "${APPLY_MODE}" == "0" ]]; then
  echo
  echo "Checks concluídos. Modo DRY-RUN (nenhum merge/push foi feito)."
  echo "Para aplicar o release:"
  echo "  $0 --apply"
  echo
  echo "Comandos que serão executados no apply:"
  echo "  git checkout ${RELEASE_BRANCH}"
  echo "  git pull --ff-only origin ${RELEASE_BRANCH}"
  echo "  git merge --no-ff ${DEV_BRANCH}"
  echo "  git push origin ${RELEASE_BRANCH}"
  if [[ "${CURRENT_BRANCH}" != "${DEV_BRANCH}" ]]; then
    echo "  git checkout ${CURRENT_BRANCH}"
  fi
  exit 0
fi

echo "==> Aplicando release em ${RELEASE_BRANCH}..."
git checkout "${RELEASE_BRANCH}"
git pull --ff-only origin "${RELEASE_BRANCH}"
git merge --no-ff "${DEV_BRANCH}" -m "release: merge ${DEV_BRANCH} into ${RELEASE_BRANCH}"
git push origin "${RELEASE_BRANCH}"

if [[ "${CURRENT_BRANCH}" != "${RELEASE_BRANCH}" ]]; then
  git checkout "${CURRENT_BRANCH}"
fi

echo "Release concluído com sucesso."
