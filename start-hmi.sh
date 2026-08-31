#!/usr/bin/env bash
echo "========================================================"
echo "Starting Innovance 6-Head CNC Angle Line Full-Stack HMI"
echo "========================================================"

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
cd "$DIR"

# Push DB schema if needed
cd "$DIR/apps/server"
npx prisma db push --skip-generate >/dev/null 2>&1

# Launch full-stack
cd "$DIR"
npm run dev
