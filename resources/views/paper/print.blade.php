<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - Print</title>
    <style>
        /* ============================================
           TURABIAN STYLE - WCBC FORMAT
           ============================================ */

        /* Base Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Page Setup - Letter size with 1-inch margins */
        @page {
            size: letter;
            margin: 1in;
        }

        /* First content page - number at bottom center */
        @page first-content {
            @bottom-center {
                content: counter(page);
                font-family: "Times New Roman", Times, serif;
                font-size: 12pt;
            }
        }

        /* Subsequent pages - number at top right */
        @page content {
            @top-right {
                content: counter(page);
                font-family: "Times New Roman", Times, serif;
                font-size: 12pt;
            }
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 2; /* Double-spaced */
            color: #000;
            background: white;
            counter-reset: page 0;
        }

        /* ============================================
           PAGE BREAKS
           ============================================ */
        .page-break {
            page-break-after: always;
            break-after: page;
        }

        .no-break {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        /* ============================================
           TITLE PAGE (No page number displayed)
           ============================================ */
        .title-page {
            height: 9in;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            line-height: 2;
        }

        .title-page .paper-title {
            font-size: 12pt;
            font-weight: normal;
            margin-bottom: 24pt; /* Double-space equivalent */
            line-height: 2;
        }

        .title-page .author {
            font-size: 12pt;
            margin-bottom: 24pt;
            line-height: 2;
        }

        .title-page .course {
            font-size: 12pt;
            margin-bottom: 24pt;
            line-height: 2;
        }

        .title-page .date {
            font-size: 12pt;
            line-height: 2;
        }

        /* ============================================
           TABLE OF CONTENTS (Roman numerals: ii, iii...)
           ============================================ */
        .toc-page {
            min-height: 9in;
        }

        .toc-title {
            text-align: center;
            text-transform: uppercase;
            font-size: 12pt;
            font-weight: normal;
            margin-bottom: 24pt;
            line-height: 2;
        }

        .toc-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .toc-item {
            line-height: 2;
            margin-bottom: 0;
        }

        .toc-item a {
            text-decoration: none;
            color: #000;
        }

        .toc-item.level-1 {
            margin-left: 0;
        }

        .toc-item.level-2 {
            margin-left: 0.5in;
        }

        .toc-item.level-3 {
            margin-left: 1in;
        }

        /* Page number for TOC - Roman numeral at bottom center */
        .toc-page-number {
            text-align: center;
            position: fixed;
            bottom: 0.5in;
            left: 0;
            right: 0;
            font-size: 12pt;
        }

        /* ============================================
           FIRST PAGE OF TEXT
           Title in ALL CAPS, centered, not styled
           Page number at bottom center
           ============================================ */
        .content-title {
            text-align: center;
            text-transform: uppercase;
            font-size: 12pt;
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            margin-bottom: 24pt;
            line-height: 2;
        }

        /* ============================================
           MAIN CONTENT - Double-spaced
           ============================================ */
        .content {
            text-align: left;
            line-height: 2;
        }

        .content p {
            text-indent: 0.5in;
            margin-bottom: 0;
            line-height: 2;
        }

        /* Headings - Subheadings in content */
        .content h1,
        .content h2,
        .content h3,
        .content h4,
        .content h5,
        .content h6 {
            text-indent: 0;
            font-weight: bold;
            margin-top: 24pt;
            margin-bottom: 12pt;
            line-height: 2;
            page-break-after: avoid;
            break-after: avoid;
        }

        .content h1 {
            text-align: center;
            text-transform: uppercase;
            font-size: 12pt;
        }

        .content h2 {
            text-align: center;
            font-size: 12pt;
            font-style: italic;
            font-weight: normal;
        }

        .content h3 {
            text-align: left;
            font-size: 12pt;
            font-weight: bold;
        }

        /* Lists */
        .content ul,
        .content ol {
            margin-left: 0.5in;
            margin-bottom: 0;
        }

        .content li {
            line-height: 2;
        }

        /* ============================================
           BLOCK QUOTATIONS
           - 3+ lines AND 2+ sentences
           - Single-spaced
           - Indented 4 spaces (0.5in) from left
           - If entire paragraph: first line +4 spaces
           ============================================ */
        .content blockquote,
        .content .block-quote {
            margin: 24pt 0 24pt 0.5in;
            padding: 0;
            line-height: 1; /* Single-spaced */
            text-indent: 0;
            page-break-inside: avoid;
        }

        .content blockquote p,
        .content .block-quote p {
            text-indent: 0;
            line-height: 1; /* Single-spaced */
            margin-bottom: 0;
        }

        /* Full paragraph block quote - extra indent on first line */
        .content blockquote.full-paragraph p:first-child,
        .content .block-quote.full-paragraph p:first-child {
            text-indent: 0.5in;
        }

        /* ============================================
           FOOTNOTES
           - Separated by short line
           - Full-sized number (not superscript)
           - First line indented 0.5in
           - Single-spaced
           - Blank line between footnotes
           ============================================ */

        /* Superscript reference in text */
        .footnote-ref {
            font-size: 9pt;
            vertical-align: super;
            line-height: 0;
        }

        .footnotes-section {
            margin-top: 24pt;
            padding-top: 12pt;
            position: relative;
        }

        /* Short separator line above footnotes (2 inches per Turabian) */
        .footnotes-section::before {
            content: "";
            display: block;
            width: 2in;
            height: 1px;
            background-color: #000;
            position: absolute;
            top: 0;
            left: 0;
        }

        .footnote {
            text-indent: 0.5in; /* First line indented */
            line-height: 1; /* Single-spaced */
            margin-bottom: 12pt; /* Blank line between footnotes */
            font-size: 12pt;
            text-align: left;
        }

        .footnote-number {
            font-size: 12pt; /* Full-sized, NOT superscript */
            vertical-align: baseline;
        }

        /* ============================================
           BIBLIOGRAPHY
           - Title centered, ALL CAPS, not styled
           - Hanging indent (0.5in after first line)
           - Single-spaced entries
           - Double-space between entries
           - Alphabetical by author's last name
           ============================================ */
        .bibliography-section {
            page-break-before: always;
        }

        .bibliography-title {
            text-align: center;
            text-transform: uppercase;
            font-size: 12pt;
            font-weight: normal;
            font-style: normal;
            margin-bottom: 24pt;
            line-height: 2;
        }

        .bibliography-entry {
            text-indent: -0.5in; /* Hanging indent */
            margin-left: 0.5in;
            line-height: 1; /* Single-spaced within entry */
            margin-bottom: 24pt; /* Double-space between entries */
            font-size: 12pt;
        }

        /* Bold and Italic */
        strong, b {
            font-weight: bold;
        }

        em, i {
            font-style: italic;
        }

        /* ============================================
           PRINT STYLES
           ============================================ */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none !important;
            }

            a {
                text-decoration: none;
                color: #000;
            }

            .page-break {
                page-break-after: always;
                break-after: page;
            }
        }

        /* ============================================
           SCREEN PREVIEW STYLES
           ============================================ */
        @media screen {
            body {
                max-width: 8.5in;
                margin: 20px auto;
                padding: 0;
                background: #f5f5f5;
            }

            .page-wrapper {
                background: white;
                padding: 1in;
                margin-bottom: 20px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.2);
                min-height: 11in;
                position: relative;
            }

            .print-instructions {
                background: #d4edda;
                border: 1px solid #c3e6cb;
                color: #155724;
                padding: 15px 20px;
                margin: 0 auto 20px auto;
                border-radius: 4px;
                font-family: Arial, sans-serif;
                font-size: 14px;
                max-width: 8.5in;
            }

            .print-instructions h3 {
                margin-bottom: 10px;
                font-size: 16px;
                text-transform: none;
                text-align: left;
            }

            .print-instructions ol {
                margin-left: 20px;
                line-height: 1.6;
            }

            .print-instructions li {
                margin-bottom: 5px;
                text-indent: 0;
            }

            .print-btn {
                background: #007bff;
                color: white;
                border: none;
                padding: 10px 20px;
                font-size: 14px;
                border-radius: 4px;
                cursor: pointer;
                margin-top: 10px;
            }

            .print-btn:hover {
                background: #0056b3;
            }

            .page-label {
                position: absolute;
                top: -22px;
                left: 0;
                font-size: 11px;
                color: #666;
                font-family: Arial, sans-serif;
            }

            .screen-page-number {
                text-align: center;
                font-size: 12pt;
                margin-top: 24pt;
                font-family: "Times New Roman", Times, serif;
            }

            .screen-page-number.top-right {
                text-align: right;
                margin-top: 0;
                margin-bottom: 24pt;
            }
        }
    </style>
</head>
<body>
    <div class="print-instructions no-print">
        <h3>Ready to Save as PDF (Turabian Format)</h3>
        <ol>
            <li>Click <strong>"Print to PDF"</strong> below (or press Ctrl+P / Cmd+P)</li>
            <li>Select <strong>"Save as PDF"</strong> as destination</li>
            <li>Paper size: <strong>Letter</strong></li>
            <li>Margins: <strong>None</strong> (margins are built into the document)</li>
            <li>Uncheck "Headers and footers" if shown</li>
            <li>Click <strong>Save</strong></li>
        </ol>
        <button class="print-btn" onclick="window.print()">Print to PDF</button>
    </div>

    {{-- ==================== TITLE PAGE ==================== --}}
    <div class="page-wrapper">
        <div class="page-label no-print">Title Page (no page number)</div>
        <div class="title-page">
            <div class="paper-title">{{ $title }}</div>
            <div class="author">{{ $author }}</div>
            @if($course)
                <div class="course">{{ $course }}</div>
            @endif
            @if($date)
                <div class="date">{{ $date }}</div>
            @endif
        </div>
    </div>
    <div class="page-break"></div>

    {{-- ==================== TABLE OF CONTENTS ==================== --}}
    @if(isset($toc) && count($toc) > 0)
    <div class="page-wrapper toc-page">
        <div class="page-label no-print">Table of Contents (page ii)</div>
        <div class="toc-title">CONTENTS</div>
        <ul class="toc-list">
            @foreach($toc as $item)
                <li class="toc-item level-{{ $item['level'] }}">
                    <a href="#{{ $item['id'] }}">{{ $item['text'] }}</a>
                </li>
            @endforeach
        </ul>
        <div class="screen-page-number no-print">ii</div>
    </div>
    <div class="page-break"></div>
    @endif

    {{-- ==================== CONTENT (Page 1+) ==================== --}}
    <div class="page-wrapper">
        <div class="page-label no-print">Page 1 (first page - number at bottom center)</div>

        {{-- Title in ALL CAPS on first content page --}}
        <div class="content-title">{{ strtoupper($title) }}</div>

        {{-- Main content --}}
        <div class="content">
            {!! $content !!}
        </div>

        {{-- Footnotes Section --}}
        @if(count($footnotes) > 0)
            <div class="footnotes-section">
                @foreach($footnotes as $number => $footnoteContent)
                    <div class="footnote">
                        <span class="footnote-number">{{ $number }}.</span> {!! $footnoteContent !!}
                    </div>
                @endforeach
            </div>
        @endif

        <div class="screen-page-number no-print">1</div>
    </div>

    {{-- ==================== BIBLIOGRAPHY (if provided) ==================== --}}
    @if(isset($bibliography) && count($bibliography) > 0)
    <div class="page-break"></div>
    <div class="page-wrapper bibliography-section">
        <div class="page-label no-print">Bibliography</div>
        <div class="screen-page-number top-right no-print">{{ count($toc) > 0 ? '2' : '2' }}</div>
        <div class="bibliography-title">BIBLIOGRAPHY</div>
        @foreach($bibliography as $entry)
            <div class="bibliography-entry">{!! $entry !!}</div>
        @endforeach
    </div>
    @endif
</body>
</html>
