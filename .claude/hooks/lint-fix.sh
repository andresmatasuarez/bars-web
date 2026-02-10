#!/bin/bash
INPUT=$(cat)
FILE_PATH=$(echo "$INPUT" | jq -r '.tool_input.file_path // empty')

# Only run on TS/TSX files
if [[ "$FILE_PATH" != *.ts && "$FILE_PATH" != *.tsx ]]; then
  exit 0
fi

# Determine workspace from file path
if [[ "$FILE_PATH" == */themes/bars2026/* ]]; then
  npm run lint:autofix -w themes/bars2026 2>/dev/null
elif [[ "$FILE_PATH" == */themes/bars2013/* ]]; then
  npm run lint:autofix -w themes/bars2013 2>/dev/null
fi

exit 0
