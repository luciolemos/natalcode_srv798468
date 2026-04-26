<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script must be run from CLI.\n");
    exit(2);
}

$cloverPath = $argv[1] ?? 'build/logs/clover.xml';
$minPercentRaw = $argv[2] ?? '55';

if (!is_file($cloverPath)) {
    fwrite(STDERR, "Clover file not found: {$cloverPath}\n");
    exit(2);
}

if (!is_numeric($minPercentRaw)) {
    fwrite(STDERR, "Invalid minimum percentage: {$minPercentRaw}\n");
    exit(2);
}

$minPercent = (float) $minPercentRaw;
if ($minPercent < 0 || $minPercent > 100) {
    fwrite(STDERR, "Minimum percentage must be between 0 and 100.\n");
    exit(2);
}

libxml_use_internal_errors(true);
$xml = simplexml_load_file($cloverPath);

if ($xml === false) {
    fwrite(STDERR, "Failed to parse Clover XML: {$cloverPath}\n");

    foreach (libxml_get_errors() as $error) {
        fwrite(STDERR, trim($error->message) . "\n");
    }

    exit(2);
}

$projectMetrics = $xml->project->metrics ?? null;
if ($projectMetrics === null) {
    fwrite(STDERR, "Clover metrics not found at project level.\n");
    exit(2);
}

$totalStatements = (int) ($projectMetrics['statements'] ?? 0);
$coveredStatements = (int) ($projectMetrics['coveredstatements'] ?? 0);

if ($totalStatements <= 0) {
    fwrite(STDERR, "Total statements is zero; cannot evaluate coverage.\n");
    exit(2);
}

$coverage = ($coveredStatements / $totalStatements) * 100;

printf(
    "Coverage: %.2f%% (%d/%d statements) | Minimum required: %.2f%%\n",
    $coverage,
    $coveredStatements,
    $totalStatements,
    $minPercent
);

if ($coverage + 1e-9 < $minPercent) {
    fwrite(STDERR, "Coverage gate failed.\n");
    exit(1);
}

echo "Coverage gate passed.\n";
exit(0);
