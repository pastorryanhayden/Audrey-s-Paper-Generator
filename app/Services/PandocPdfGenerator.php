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
        $markdown = $this->buildMarkdown($data);
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

        $result = Process::env([
            'PATH' => '/usr/bin:/usr/local/bin:/bin',
            'HOME' => sys_get_temp_dir(),
        ])->run(implode(' ', array_map('escapeshellarg', $command)));

        // Clean up temp markdown file
        @unlink($tempMd);

        if (!$result->successful()) {
            throw new \RuntimeException('Pandoc failed: ' . $result->errorOutput());
        }

        return $tempPdf;
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
