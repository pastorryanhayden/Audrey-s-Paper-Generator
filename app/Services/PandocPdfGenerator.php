<?php

namespace App\Services;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class PandocPdfGenerator
{
    protected string $tempDir;

    public function __construct()
    {
        $this->tempDir = storage_path('app/temp');
        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0755, true);
        }
    }

    /**
     * Generate a PDF from parsed content
     */
    public function generate(array $data): string
    {
        // Extract bibliography entries first
        $rawContent = $data['rawContent'] ?? '';
        $bibliography = [];
        $rawContent = $this->extractBibliography($rawContent, $bibliography);

        $markdown = $rawContent;
        $tempMd = $this->tempDir . '/' . Str::uuid() . '.md';
        $tempPdf = $this->tempDir . '/' . Str::uuid() . '.pdf';

        file_put_contents($tempMd, $markdown);

        $templatePath = resource_path('templates/turabian.latex');

        $command = [
            '/usr/bin/pandoc',
            $tempMd,
            '-o', $tempPdf,
            '--pdf-engine=/usr/bin/pdflatex',
            '--template=' . $templatePath,
            '-V', 'title=' . $this->escapeLatex($data['title']),
            '-V', 'author=' . $this->escapeLatex($data['author']),
            '-V', 'geometry:margin=1in',
            '-V', 'fontsize=12pt',
            '-V', 'documentclass=article',
            '-V', 'linestretch=2',
        ];

        if (!empty($data['course'])) {
            $command[] = '-V';
            $command[] = 'course=' . $this->escapeLatex($data['course']);
        }

        if (!empty($data['date'])) {
            $command[] = '-V';
            $command[] = 'date=' . $this->escapeLatex($data['date']);
        }

        // Add bibliography entries (already escaped in extractBibliography)
        foreach ($bibliography as $entry) {
            $command[] = '-V';
            $command[] = 'bibliography=' . $entry;
        }

        $commandString = implode(' ', array_map('escapeshellarg', $command));

        // Debug: log the command
        \Log::debug('Pandoc command: ' . $commandString);
        \Log::debug('Bibliography entries: ' . json_encode($bibliography));

        $result = Process::env([
            'PATH' => '/usr/bin:/usr/local/bin:/bin',
            'HOME' => sys_get_temp_dir(),
        ])->run($commandString);

        // Clean up temp markdown file
        @unlink($tempMd);

        if (!$result->successful()) {
            throw new \RuntimeException('Pandoc failed: ' . $result->errorOutput());
        }

        return $tempPdf;
    }

    /**
     * Extract bibliography entries from markdown
     * Format: [bib]: Entry text
     */
    protected function extractBibliography(string $markdown, array &$bibliography): string
    {
        // Normalize line endings
        $markdown = str_replace("\r\n", "\n", $markdown);
        $markdown = str_replace("\r", "\n", $markdown);

        // Debug: log first and last 500 chars to see content
        \Log::debug('Raw content (first 500 chars): ' . substr($markdown, 0, 500));
        \Log::debug('Raw content (last 1000 chars): ' . substr($markdown, -1000));
        \Log::debug('Looking for [bib]: pattern...');

        // Match [bib]: Entry text lines (allow leading whitespace, case insensitive)
        if (preg_match_all('/^\s*\[bib\]:\s*(.+)$/im', $markdown, $matches)) {
            \Log::debug('Found ' . count($matches[1]) . ' bibliography entries');
            foreach ($matches[1] as $entry) {
                $entry = trim($entry);
                if (!empty($entry)) {
                    // Escape special characters for LaTeX (but not backslash)
                    $entry = $this->escapeLatexForBibliography($entry);
                    // Convert markdown italics to LaTeX
                    $entry = preg_replace('/\*([^*]+)\*/', '\\textit{$1}', $entry);
                    $bibliography[] = $entry;
                }
            }
            // Remove bibliography lines from main content
            $markdown = preg_replace('/^\s*\[bib\]:\s*.+$/im', '', $markdown);
        } else {
            \Log::debug('No [bib]: entries found');
            // Check if bib appears at all
            if (strpos($markdown, 'bib') !== false) {
                \Log::debug('Found "bib" somewhere in content');
            }
        }

        // Sort bibliography alphabetically
        sort($bibliography);

        return trim($markdown);
    }

    /**
     * Escape LaTeX characters for bibliography (preserves backslash for commands)
     */
    protected function escapeLatexForBibliography(string $text): string
    {
        $replacements = [
            '&' => '\\&',
            '%' => '\\%',
            '#' => '\\#',
            '_' => '\\_',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }

    /**
     * Build the markdown content with footnotes
     */
    protected function buildMarkdown(array $data): string
    {
        $content = $data['rawContent'] ?? '';

        return $content;
    }

    /**
     * Escape special LaTeX characters
     */
    protected function escapeLatex(string $text): string
    {
        $replacements = [
            '\\' => '\\textbackslash{}',
            '{' => '\\{',
            '}' => '\\}',
            '$' => '\\$',
            '&' => '\\&',
            '%' => '\\%',
            '#' => '\\#',
            '_' => '\\_',
            '^' => '\\textasciicircum{}',
            '~' => '\\textasciitilde{}',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }

    /**
     * Clean up old temp files
     */
    public function cleanup(string $pdfPath): void
    {
        @unlink($pdfPath);
    }
}
