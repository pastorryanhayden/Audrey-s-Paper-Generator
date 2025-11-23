<?php

namespace App\Services;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;

class TurabianParser
{
    protected array $footnotes = [];
    protected array $footnoteDefinitions = [];
    protected array $tableOfContents = [];
    protected array $bibliography = [];
    protected int $currentFootnoteNumber = 0;
    protected ?string $lastFootnoteContent = null;
    protected int $headingCounter = 0;

    /**
     * Parse markdown content and return structured data for PDF generation
     */
    public function parse(string $markdown): array
    {
        // Reset state
        $this->footnotes = [];
        $this->footnoteDefinitions = [];
        $this->tableOfContents = [];
        $this->bibliography = [];
        $this->currentFootnoteNumber = 0;
        $this->lastFootnoteContent = null;
        $this->headingCounter = 0;

        // Extract bibliography section first
        $markdown = $this->extractBibliography($markdown);

        // Extract headings for TOC before processing
        $this->extractHeadings($markdown);

        // Extract footnote definitions first
        $markdown = $this->extractFootnoteDefinitions($markdown);

        // Process block quotations
        $markdown = $this->processBlockQuotations($markdown);

        // Convert markdown to HTML
        $html = $this->convertMarkdownToHtml($markdown);

        // Process footnote references in HTML
        $html = $this->processFootnoteReferences($html);

        // Add IDs to headings for TOC linking
        $html = $this->addHeadingIds($html);

        return [
            'html' => $html,
            'footnotes' => $this->footnotes,
            'toc' => $this->tableOfContents,
            'bibliography' => $this->bibliography,
        ];
    }

    /**
     * Extract bibliography entries from markdown
     * Format: {{bibliography}}
     *         Entry 1
     *         Entry 2
     *         {{/bibliography}}
     * Or lines starting with [bib]: Entry text
     */
    protected function extractBibliography(string $markdown): string
    {
        // Method 1: Block format {{bibliography}}...{{/bibliography}}
        if (preg_match('/\{\{bibliography\}\}(.+?)\{\{\/bibliography\}\}/s', $markdown, $match)) {
            $bibContent = trim($match[1]);
            $entries = preg_split('/\n(?=\S)/', $bibContent);
            foreach ($entries as $entry) {
                $entry = trim($entry);
                if (!empty($entry)) {
                    // Process markdown in entry (for italics, etc.)
                    $entry = $this->convertMarkdownToHtml($entry);
                    $entry = strip_tags($entry, '<em><i><strong><b>');
                    $this->bibliography[] = trim($entry);
                }
            }
            $markdown = preg_replace('/\{\{bibliography\}\}.+?\{\{\/bibliography\}\}/s', '', $markdown);
        }

        // Method 2: Line format [bib]: Entry text
        if (preg_match_all('/^\[bib\]:\s*(.+)$/m', $markdown, $matches)) {
            foreach ($matches[1] as $entry) {
                $entry = trim($entry);
                if (!empty($entry)) {
                    $entry = $this->convertMarkdownToHtml($entry);
                    $entry = strip_tags($entry, '<em><i><strong><b>');
                    $this->bibliography[] = trim($entry);
                }
            }
            $markdown = preg_replace('/^\[bib\]:\s*.+$/m', '', $markdown);
        }

        // Sort bibliography alphabetically
        sort($this->bibliography);

        return trim($markdown);
    }

    /**
     * Extract headings from markdown for table of contents
     */
    protected function extractHeadings(string $markdown): void
    {
        // Match markdown headings (# Heading, ## Heading, etc.)
        preg_match_all('/^(#{1,6})\s+(.+)$/m', $markdown, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $level = strlen($match[1]); // Number of # symbols
            $text = trim($match[2]);
            // Remove any markdown formatting from heading text
            $text = preg_replace('/\*\*(.+?)\*\*/', '$1', $text); // Bold
            $text = preg_replace('/\*(.+?)\*/', '$1', $text); // Italic
            $text = preg_replace('/`(.+?)`/', '$1', $text); // Code

            $this->headingCounter++;
            $this->tableOfContents[] = [
                'level' => $level,
                'text' => $text,
                'id' => 'heading-' . $this->headingCounter,
            ];
        }
    }

    /**
     * Add IDs to HTML headings for TOC linking
     */
    protected function addHeadingIds(string $html): string
    {
        $counter = 0;
        $html = preg_replace_callback('/<(h[1-6])>(.+?)<\/\1>/i', function ($match) use (&$counter) {
            $counter++;
            $tag = $match[1];
            $content = $match[2];
            return '<' . $tag . ' id="heading-' . $counter . '">' . $content . '</' . $tag . '>';
        }, $html);

        return $html;
    }

    /**
     * Extract footnote definitions from markdown
     * Format: [^1]: Footnote content here
     */
    protected function extractFootnoteDefinitions(string $markdown): string
    {
        // Match multi-line footnote definitions
        $pattern = '/\[\^(\d+)\]:\s*(.+?)(?=\n\[\^|\n\n|\z)/s';

        preg_match_all($pattern, $markdown, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $number = $match[1];
            $content = trim($match[2]);
            // Clean up multi-line content
            $content = preg_replace('/\n\s+/', ' ', $content);
            $this->footnoteDefinitions[$number] = $content;
        }

        // Remove footnote definitions from main content
        $markdown = preg_replace('/\[\^(\d+)\]:\s*.+?(?=\n\[\^|\n\n|\z)/s', '', $markdown);

        return trim($markdown);
    }

    /**
     * Process block quotations - detect 3+ lines with 2+ sentences
     */
    protected function processBlockQuotations(string $markdown): string
    {
        // Split into paragraphs
        $paragraphs = preg_split('/\n{2,}/', $markdown);
        $processed = [];

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);

            // Skip empty paragraphs
            if (empty($paragraph)) {
                continue;
            }

            // Check if this looks like a quote (starts with > or is wrapped in custom markers)
            if (preg_match('/^>\s*/', $paragraph)) {
                // Standard markdown blockquote - process it
                $quoteContent = preg_replace('/^>\s*/m', '', $paragraph);
                $processed[] = $this->formatBlockQuotation($quoteContent);
            } elseif (preg_match('/^\{\{quote\}\}(.+?)\{\{\/quote\}\}/s', $paragraph, $match)) {
                // Custom quote markers
                $processed[] = $this->formatBlockQuotation(trim($match[1]));
            } elseif (preg_match('/^\{\{blockquote(?:\s+paragraph)?\}\}(.+?)\{\{\/blockquote\}\}/s', $paragraph, $match)) {
                // Extended blockquote with optional paragraph flag
                $isParagraph = strpos($paragraph, 'paragraph') !== false;
                $processed[] = $this->formatBlockQuotation(trim($match[1]), $isParagraph);
            } else {
                $processed[] = $paragraph;
            }
        }

        return implode("\n\n", $processed);
    }

    /**
     * Determine if text qualifies as a block quotation
     * Requirements: 3+ lines AND 2+ sentences
     */
    protected function isBlockQuotation(string $text): bool
    {
        // Count lines (rough estimate based on ~80 chars per line)
        $lineCount = ceil(strlen($text) / 80);

        // Count sentences (periods, exclamation marks, question marks followed by space or end)
        $sentenceCount = preg_match_all('/[.!?]+(?:\s|$)/', $text);

        return $lineCount >= 3 && $sentenceCount >= 2;
    }

    /**
     * Format a block quotation with proper Turabian styling
     */
    protected function formatBlockQuotation(string $content, bool $isFullParagraph = false): string
    {
        // Check if it qualifies as block quotation
        if ($this->isBlockQuotation($content)) {
            if ($isFullParagraph) {
                // Full paragraph from source - extra first line indent, parenthetical citation
                return '<div class="block-quote full-paragraph">' . $content . '</div>';
            } else {
                // Short block quote - regular indent, superscript footnote allowed
                return '<div class="block-quote">' . $content . '</div>';
            }
        }

        // Not a block quote, return as regular paragraph
        return $content;
    }

    /**
     * Convert markdown to HTML using CommonMark
     */
    protected function convertMarkdownToHtml(string $markdown): string
    {
        $environment = new Environment([
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());

        $converter = new CommonMarkConverter([], $environment);

        return $converter->convert($markdown)->getContent();
    }

    /**
     * Process footnote references in HTML and build footnotes array
     */
    protected function processFootnoteReferences(string $html): string
    {
        // Match [^1] style references
        $pattern = '/\[\^(\d+)\]/';

        $html = preg_replace_callback($pattern, function ($match) {
            $refNumber = $match[1];
            $this->currentFootnoteNumber++;

            // Get the footnote content
            $content = $this->footnoteDefinitions[$refNumber] ?? "Footnote $refNumber";

            // Check for Ibid. logic
            if ($this->lastFootnoteContent !== null && $content === $this->lastFootnoteContent) {
                $displayContent = 'Ibid.';
            } else {
                $displayContent = $content;
                $this->lastFootnoteContent = $content;
            }

            $this->footnotes[$this->currentFootnoteNumber] = $displayContent;

            // Return superscript reference
            return '<sup class="footnote-ref">' . $this->currentFootnoteNumber . '</sup>';
        }, $html);

        return $html;
    }

    /**
     * Count sentences in a text
     */
    protected function countSentences(string $text): int
    {
        return preg_match_all('/[.!?]+(?:\s|$)/', $text);
    }

    /**
     * Estimate line count for text (based on ~80 characters per line)
     */
    protected function estimateLineCount(string $text, int $charsPerLine = 80): int
    {
        return (int) ceil(strlen($text) / $charsPerLine);
    }
}
