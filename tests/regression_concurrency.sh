#!/usr/bin/env bash
set -euo pipefail

BASE_URL="${1:-http://127.0.0.1:18080}"
REQUESTS="${REQUESTS:-200}"
PARALLEL="${PARALLEL:-40}"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"
DB_FILE="${REPO_DIR}/storage/random_numbers.sqlite"
JSON_FILE="${REPO_DIR}/storage/random_numbers.json"

db_count() {
  php -r '
    $sqlite = $argv[1];
    $json = $argv[2];

    if (class_exists("PDO") && in_array("sqlite", PDO::getAvailableDrivers(), true) && file_exists($sqlite)) {
        try {
            $pdo = new PDO("sqlite:" . $sqlite);
            $stmt = $pdo->query("SELECT COUNT(*) FROM random_numbers");
            echo (int) $stmt->fetchColumn();
            exit;
        } catch (Throwable) {
            // fallback to JSON storage below
        }
    }

    try {
        if (!file_exists($json)) {
            echo 0;
            exit;
        }

        $content = file_get_contents($json);
        if ($content === false || trim($content) === "") {
            echo 0;
            exit;
        }

        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        echo is_array($data) ? count($data) : 0;
    } catch (Throwable) {
        echo 0;
    }
  ' "${DB_FILE}" "${JSON_FILE}"
}

before="$(db_count)"

seq 1 "${REQUESTS}" | xargs -P "${PARALLEL}" -I{} curl -fsS "${BASE_URL}/api/random" > /dev/null

after="$(db_count)"
delta=$((after - before))

echo "before=${before} after=${after} delta=${delta} expected=${REQUESTS}"

if [[ "${delta}" -ne "${REQUESTS}" ]]; then
  echo "FAILED: detected lost writes under concurrent load" >&2
  exit 1
fi

echo "OK regression_concurrency.sh passed"
