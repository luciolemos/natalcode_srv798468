<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script must be run from CLI.\n");
    exit(2);
}

$cloverPath = $argv[1] ?? 'build/logs/clover.xml';
$minPercentRaw = $argv[2] ?? '55';
$nextTargetPercentRaw = $argv[3] ?? '';
$criticalPathTargetsRaw = $argv[4] ?? '';

if (!is_file($cloverPath)) {
    fwrite(STDERR, "Clover file not found: {$cloverPath}\n");
    exit(2);
}

/**
 * @param mixed $value
 */
$normalizePercent = static function ($value, string $label): float {
    if (!is_numeric($value)) {
        fwrite(STDERR, "{$label} must be numeric: {$value}\n");
        exit(2);
    }

    $percent = (float) $value;
    if ($percent < 0 || $percent > 100) {
        fwrite(STDERR, "{$label} must be between 0 and 100.\n");
        exit(2);
    }

    return $percent;
};

$minPercent = $normalizePercent($minPercentRaw, 'Minimum percentage');

$nextTargetPercent = null;
if (trim((string) $nextTargetPercentRaw) !== '') {
    $nextTargetPercent = $normalizePercent($nextTargetPercentRaw, 'Next target percentage');

    if ($nextTargetPercent + 1e-9 < $minPercent) {
        fwrite(STDERR, "Next target percentage must be >= minimum percentage.\n");
        exit(2);
    }
}

$criticalPathTargets = [];
if (trim((string) $criticalPathTargetsRaw) !== '') {
    foreach (explode(',', (string) $criticalPathTargetsRaw) as $rawTarget) {
        $targetEntry = trim($rawTarget);

        if ($targetEntry === '') {
            continue;
        }

        $parts = explode('=', $targetEntry, 2);
        if (count($parts) !== 2) {
            fwrite(STDERR, "Invalid critical path target entry: {$targetEntry}\n");
            exit(2);
        }

        $path = trim($parts[0]);
        $targetPercent = trim($parts[1]);

        if ($path === '') {
            fwrite(STDERR, "Critical path target entry has an empty path: {$targetEntry}\n");
            exit(2);
        }

        $criticalPathTargets[] = [
            'path' => ltrim(str_replace('\\', '/', $path), '/'),
            'target' => $normalizePercent($targetPercent, "Critical target for {$path}"),
        ];
    }
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

if ($nextTargetPercent !== null) {
    if ($coverage + 1e-9 < $nextTargetPercent) {
        fwrite(
            STDERR,
            sprintf(
                "Advisory: next target not reached yet (%.2f%% < %.2f%%).\n",
                $coverage,
                $nextTargetPercent
            )
        );
    } else {
        printf(
            "Advisory: next target reached (%.2f%% >= %.2f%%).\n",
            $coverage,
            $nextTargetPercent
        );
    }
}

if ($criticalPathTargets !== []) {
    $projectRoot = str_replace('\\', '/', realpath(__DIR__ . '/..') ?: dirname(__DIR__));
    $projectRoot = rtrim($projectRoot, '/');

    /** @var array<int, array{path: string, statements: int, covered: int}> $fileMetrics */
    $fileMetrics = [];

    /** @var array<int, \SimpleXMLElement>|false $fileNodes */
    $fileNodes = $xml->xpath('//file');

    if (is_array($fileNodes)) {
        foreach ($fileNodes as $fileNode) {
            $filePath = trim((string) ($fileNode['name'] ?? ''));
            if ($filePath === '') {
                continue;
            }

            /** @var array<int, \SimpleXMLElement>|false $metricsNodes */
            $metricsNodes = $fileNode->xpath('./metrics');
            if (!is_array($metricsNodes) || $metricsNodes === []) {
                continue;
            }

            $metricsNode = $metricsNodes[0];
            $statements = (int) ($metricsNode['statements'] ?? 0);
            $covered = (int) ($metricsNode['coveredstatements'] ?? 0);

            if ($statements <= 0) {
                continue;
            }

            $fileMetrics[] = [
                'path' => str_replace('\\', '/', $filePath),
                'statements' => $statements,
                'covered' => $covered,
            ];
        }
    }

    echo "Critical path targets (advisory):\n";

    foreach ($criticalPathTargets as $criticalTarget) {
        $pathPrefix = $criticalTarget['path'];
        $targetPercent = (float) $criticalTarget['target'];
        $pathStatements = 0;
        $pathCovered = 0;

        foreach ($fileMetrics as $fileMetric) {
            $filePath = $fileMetric['path'];
            $relativeFilePath = $filePath;

            if (str_starts_with($filePath, $projectRoot . '/')) {
                $relativeFilePath = substr($filePath, strlen($projectRoot) + 1);
            }

            if (
                $relativeFilePath !== $pathPrefix
                && !str_starts_with($relativeFilePath, $pathPrefix . '/')
            ) {
                continue;
            }

            $pathStatements += (int) $fileMetric['statements'];
            $pathCovered += (int) $fileMetric['covered'];
        }

        if ($pathStatements <= 0) {
            fwrite(STDERR, sprintf(" - %s: no statements found (advisory).\n", $pathPrefix));
            continue;
        }

        $pathCoverage = ($pathCovered / $pathStatements) * 100;

        printf(
            " - %s: %.2f%% (%d/%d statements) | advisory target: %.2f%%\n",
            $pathPrefix,
            $pathCoverage,
            $pathCovered,
            $pathStatements,
            $targetPercent
        );

        if ($pathCoverage + 1e-9 < $targetPercent) {
            fwrite(
                STDERR,
                sprintf(
                    "   Advisory: target not reached for %s (%.2f%% < %.2f%%).\n",
                    $pathPrefix,
                    $pathCoverage,
                    $targetPercent
                )
            );
        }
    }
}

exit(0);
