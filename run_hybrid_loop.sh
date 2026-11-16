#!/usr/bin/env bash
set -e

MAX_PASSES=4

for ((pass=1; pass<=MAX_PASSES; pass++)); do
  echo "=================================================="
  echo "STARTING HYBRID LOOP PASS $pass OF $MAX_PASSES"
  echo "=================================================="

  # 1. WORKER: Codex CLI on macOS (reads prompt via stdin '-' so -i only captures layout.png)
  codex -a never exec --sandbox workspace-write -i contract/layout.png - << 'PROMPT'
Read contract/CONTRACT.md and loop_state/PROGRESS.md.
Update resources/views/welcome.blade.php so it visually matches the attached contract/layout.png (including the large translucent cloud behind 8°, centered header alignment, crescent moon icons at 3am/5am/6am/7am, yellow sun at 8am, and 10-day range bars) and satisfies CONTRACT.md.
Do NOT modify anything inside contract/.
PROMPT

  git add resources/views/welcome.blade.php
  git commit -m "feat(loop): pass $pass codex worker update" --allow-empty

  # 2. VERIFIER + CHECKER: Docker with contract/ mounted read-only (:ro)
  if docker run --rm \
    --env-file .env.loop \
    -e APP_URL="http://laravel-iphone-loop.test/" \
    --add-host=laravel-iphone-loop.test:host-gateway \
    -v "$(pwd):/app" \
    -v /app/node_modules \
    -v "$(pwd)/contract:/app/contract:ro" \
    laravel-loop-agent \
    python3 docker/check_only.py "$pass"; then
    echo "✅ LOOP EXIT: DONE!"
    exit 0
  fi
done

echo "⚠️ LOOP EXIT: NEEDS_PERSON (Reached $MAX_PASSES passes)"
exit 1
