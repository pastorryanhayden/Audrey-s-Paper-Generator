<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title }}</title>
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Page setup for PDF */
        @page {
            margin: 1in;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 2;
            color: #000;
            margin: 0;
            padding: 0;
        }

        /* Page break utility */
        .page-break {
            page-break-after: always;
        }

        /* Title Page Styles */
        .title-page {
            height: 9in;
            display: table;
            width: 100%;
            text-align: center;
        }

        .title-page-content {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            line-height: 2;
        }

        .title-page .paper-title {
            font-size: 12pt;
            font-weight: normal;
            text-transform: none;
            margin-bottom: 2em;
        }

        .title-page .author {
            font-size: 12pt;
            margin-bottom: 2em;
        }

        .title-page .course,
        .title-page .date {
            font-size: 12pt;
            margin-bottom: 1em;
        }

        /* Table of Contents Styles */
        .toc-title {
            text-align: center;
            text-transform: uppercase;
            font-size: 12pt;
            font-weight: normal;
            margin-bottom: 2em;
            line-height: 2;
        }

        .toc-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .toc-item {
            line-height: 2;
            margin-bottom: 0.5em;
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

        .toc-item.level-4,
        .toc-item.level-5,
        .toc-item.level-6 {
            margin-left: 1.5in;
        }

        .toc-page-number {
            text-align: center;
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
        }

        /* Content Page Title */
        .content-title {
            text-align: center;
            text-transform: uppercase;
            font-size: 12pt;
            font-weight: normal;
            font-style: normal;
            margin-bottom: 2em;
            line-height: 2;
        }

        /* Main Content Styles */
        .content {
            text-align: left;
            line-height: 2;
        }

        .content p {
            text-indent: 0.5in;
            margin-bottom: 0;
            line-height: 2;
        }

        /* Headers in content */
        .content h1,
        .content h2,
        .content h3,
        .content h4,
        .content h5,
        .content h6 {
            text-indent: 0;
            font-weight: bold;
            margin-top: 1em;
            margin-bottom: 0.5em;
            line-height: 2;
            page-break-after: avoid;
        }

        .content h1 {
            text-align: center;
            text-transform: uppercase;
            font-size: 12pt;
        }

        .content h2 {
            text-align: center;
            font-size: 12pt;
        }

        .content h3 {
            text-align: left;
            font-size: 12pt;
        }

        /* Lists */
        .content ul,
        .content ol {
            margin-left: 0.5in;
            margin-bottom: 1em;
            text-indent: 0;
        }

        .content li {
            text-indent: 0;
            line-height: 2;
        }

        /* Block Quotations */
        .content .block-quote,
        .content blockquote {
            margin: 1em 0;
            margin-left: 0.5in;
            margin-right: 0;
            text-indent: 0;
            line-height: 1;
            padding: 0;
        }

        .content .block-quote p,
        .content blockquote p {
            text-indent: 0;
            line-height: 1;
            margin-bottom: 0;
        }

        /* Footnote References (Superscript in text) */
        .footnote-ref {
            font-size: 9pt;
            vertical-align: super;
            line-height: 0;
        }

        /* Footnotes Section */
        .footnotes-section {
            margin-top: 2em;
            padding-top: 1em;
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
            text-indent: 0.5in;
            line-height: 1;
            margin-bottom: 1em;
            font-size: 12pt;
            text-align: left;
        }

        .footnote-number {
            font-size: 12pt;
            vertical-align: baseline;
        }

        /* Bibliography Section */
        .bibliography-section {
            page-break-before: always;
        }

        .bibliography-title {
            text-align: center;
            text-transform: uppercase;
            font-size: 12pt;
            font-weight: normal;
            font-style: normal;
            margin-bottom: 2em;
            line-height: 2;
        }

        .bibliography-entry {
            text-indent: -0.5in; /* Hanging indent */
            margin-left: 0.5in;
            line-height: 1; /* Single-spaced within entry */
            margin-bottom: 1.5em; /* Double-space between entries */
            font-size: 12pt;
        }

        /* Bold and Italic */
        strong, b {
            font-weight: bold;
        }

        em, i {
            font-style: italic;
        }

        @if(isset($preview) && $preview)
        /* ==================== PREVIEW MODE STYLES ==================== */
        html {
            background: #e0e0e0;
            padding: 20px;
        }

        body {
            background: transparent;
            max-width: 8.5in;
            margin: 0 auto;
        }

        /* Each .page represents a paper page in preview */
        .page {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            padding: 1in;
            margin-bottom: 30px;
            position: relative;
            min-height: 11in;
        }

        /* Content page can grow beyond min-height */
        .page.content-page {
            min-height: 11in;
            height: auto;
        }

        .page-break {
            display: none;
        }

        /* Page label for preview */
        .page-label {
            position: absolute;
            top: -22px;
            left: 0;
            font-size: 11px;
            color: #666;
            font-family: Arial, sans-serif;
        }

        /* Page number display in preview */
        .page-number {
            text-align: center;
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            margin-top: 2em;
            padding-top: 1em;
        }

        /* Info banner for preview */
        .preview-info {
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-family: Arial, sans-serif;
            font-size: 13px;
            max-width: 8.5in;
            margin-left: auto;
            margin-right: auto;
        }
        @endif
    </style>
</head>
<body>
    @if(isset($preview) && $preview)
    <div class="preview-info">
        <strong>Preview Mode:</strong> This is an approximate preview. The downloaded PDF will have proper page breaks, margins, and page numbering handled automatically.
    </div>
    @endif

    {{-- Title Page (Page i - no number displayed) --}}
    <div class="page">
        @if(isset($preview) && $preview)
        <div class="page-label">Title Page (i)</div>
        @endif
        <div class="title-page">
            <div class="title-page-content">
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
    </div>
    <div class="page-break"></div>

    {{-- Table of Contents Page (Page ii) --}}
    @if(isset($toc) && count($toc) > 0)
    <div class="page">
        @if(isset($preview) && $preview)
        <div class="page-label">Table of Contents (ii)</div>
        @endif
        <div class="toc-title">TABLE OF CONTENTS</div>
        <ul class="toc-list">
            @foreach($toc as $item)
                <li class="toc-item level-{{ $item['level'] }}">
                    <a href="#{{ $item['id'] }}">{{ $item['text'] }}</a>
                </li>
            @endforeach
        </ul>
        <div class="page-number">ii</div>
    </div>
    <div class="page-break"></div>
    @endif

    {{-- Content Pages (Page 1+) --}}
    <div class="page content-page">
        @if(isset($preview) && $preview)
        <div class="page-label">Page 1 (content continues with automatic page breaks in PDF)</div>
        @endif
        <div class="content-title">{{ strtoupper($title) }}</div>

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

        <div class="page-number">1</div>
    </div>

    {{-- Bibliography Page --}}
    @if(isset($bibliography) && count($bibliography) > 0)
    <div class="page-break"></div>
    <div class="page bibliography-section">
        @if(isset($preview) && $preview)
        <div class="page-label">Bibliography</div>
        @endif
        <div class="bibliography-title">Bibliography</div>
        @foreach($bibliography as $entry)
            <div class="bibliography-entry">{!! $entry !!}</div>
        @endforeach
        <div class="page-number">2</div>
    </div>
    @endif
</body>
</html>
