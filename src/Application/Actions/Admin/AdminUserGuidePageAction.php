<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\Page\AbstractPageAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AdminUserGuidePageAction extends AbstractPageAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        $readmePath = dirname(__DIR__, 4) . '/README.md';
        $readmeContent = 'README.md não encontrado.';

        if (is_file($readmePath) && is_readable($readmePath)) {
            $rawReadme = file_get_contents($readmePath);
            if (is_string($rawReadme) && $rawReadme !== '') {
                $readmeContent = $rawReadme;
            }
        }

        return $this->renderPage($response, 'pages/admin-user-guide.twig', [
            'page_title' => 'Guia do Usuário | Painel CEDE',
            'page_url' => 'https://cedern.org/painel/guia-do-usuario',
            'page_description' => 'Guia de uso do painel administrativo do CEDE.',
            'admin_project_readme_html' => $this->renderMarkdown($readmeContent),
        ]);
    }

    private function renderMarkdown(string $markdown): string
    {
        $normalized = str_replace(["\r\n", "\r"], "\n", trim($markdown));
        if ($normalized === '') {
            return '<p>README.md vazio.</p>';
        }

        $lines = explode("\n", $normalized);
        $html = [];
        $paragraph = [];
        $listItems = [];
        $codeLines = [];
        $inCodeBlock = false;

        $flushParagraph = static function () use (&$html, &$paragraph): void {
            if ($paragraph === []) {
                return;
            }

            $text = implode(' ', $paragraph);
            $html[] = '<p>' . $text . '</p>';
            $paragraph = [];
        };

        $flushList = static function () use (&$html, &$listItems): void {
            if ($listItems === []) {
                return;
            }

            $html[] = '<ul><li>' . implode('</li><li>', $listItems) . '</li></ul>';
            $listItems = [];
        };

        $flushCode = static function () use (&$html, &$codeLines): void {
            if ($codeLines === []) {
                return;
            }

            $html[] = '<pre><code>' . implode("\n", $codeLines) . '</code></pre>';
            $codeLines = [];
        };

        foreach ($lines as $line) {
            if (preg_match('/^```/', $line) === 1) {
                $flushParagraph();
                $flushList();

                if ($inCodeBlock) {
                    $flushCode();
                    $inCodeBlock = false;
                } else {
                    $inCodeBlock = true;
                }

                continue;
            }

            if ($inCodeBlock) {
                $codeLines[] = htmlspecialchars($line, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

                continue;
            }

            $trimmed = trim($line);
            if ($trimmed === '') {
                $flushParagraph();
                $flushList();

                continue;
            }

            if (preg_match('/^(#{1,6})\s+(.*)$/', $trimmed, $headingMatch) === 1) {
                $flushParagraph();
                $flushList();

                $level = strlen($headingMatch[1]);
                $content = $this->renderInlineMarkdown($headingMatch[2]);
                $html[] = sprintf('<h%d>%s</h%d>', $level, $content, $level);

                continue;
            }

            if (preg_match('/^[-*]\s+(.*)$/', $trimmed, $listMatch) === 1) {
                $flushParagraph();
                $listItems[] = $this->renderInlineMarkdown($listMatch[1]);

                continue;
            }

            $paragraph[] = $this->renderInlineMarkdown($trimmed);
        }

        $flushParagraph();
        $flushList();
        $flushCode();

        return implode("\n", $html);
    }

    private function renderInlineMarkdown(string $text): string
    {
        $escaped = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $escaped = preg_replace(
            '/\[([^\]]+)\]\((https?:\/\/[^\s)]+|\/[^\s)]+)\)/',
            '<a href="$2" target="_blank" rel="noopener noreferrer">$1</a>',
            $escaped
        ) ?? $escaped;

        $escaped = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $escaped) ?? $escaped;
        $escaped = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $escaped) ?? $escaped;
        $escaped = preg_replace('/`(.+?)`/', '<code>$1</code>', $escaped) ?? $escaped;

        return $escaped;
    }
}