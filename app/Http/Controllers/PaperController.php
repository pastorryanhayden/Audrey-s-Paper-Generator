<?php

namespace App\Http\Controllers;

use App\Services\TurabianParser;
use App\Services\PandocPdfGenerator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PaperController extends Controller
{
    public function __construct(
        protected TurabianParser $parser,
        protected PandocPdfGenerator $pdfGenerator
    ) {}

    /**
     * Display the paper input form
     */
    public function index(): View
    {
        return view('paper.form');
    }

    /**
     * Generate printable view (opens print dialog)
     */
    public function generate(Request $request): View
    {
        $validated = $request->validate([
            'title' => 'required|string|max:500',
            'author' => 'required|string|max:255',
            'course' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'content' => 'required|string',
        ]);

        // Parse the markdown content
        $parsed = $this->parser->parse($validated['content']);

        // Format the date if provided
        $formattedDate = null;
        if (!empty($validated['date'])) {
            $formattedDate = \Carbon\Carbon::parse($validated['date'])->format('F j, Y');
        }

        // Prepare data for print view
        $data = [
            'title' => $validated['title'],
            'author' => $validated['author'],
            'course' => $validated['course'] ?? null,
            'date' => $formattedDate,
            'content' => $parsed['html'],
            'footnotes' => $parsed['footnotes'],
            'toc' => $parsed['toc'],
            'bibliography' => $parsed['bibliography'] ?? [],
            'printMode' => true,
        ];

        return view('paper.print', $data);
    }

    /**
     * Preview the paper (returns HTML for preview)
     */
    public function preview(Request $request): View
    {
        $validated = $request->validate([
            'title' => 'required|string|max:500',
            'author' => 'required|string|max:255',
            'course' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'content' => 'required|string',
        ]);

        // Parse the markdown content
        $parsed = $this->parser->parse($validated['content']);

        // Format the date if provided
        $formattedDate = null;
        if (!empty($validated['date'])) {
            $formattedDate = \Carbon\Carbon::parse($validated['date'])->format('F j, Y');
        }

        // Prepare data for preview
        $data = [
            'title' => $validated['title'],
            'author' => $validated['author'],
            'course' => $validated['course'] ?? null,
            'date' => $formattedDate,
            'content' => $parsed['html'],
            'footnotes' => $parsed['footnotes'],
            'toc' => $parsed['toc'],
            'bibliography' => $parsed['bibliography'] ?? [],
            'preview' => true,
        ];

        return view('paper.pdf', $data);
    }

    /**
     * Download PDF using Pandoc
     */
    public function download(Request $request): BinaryFileResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:500',
            'author' => 'required|string|max:255',
            'course' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'content' => 'required|string',
        ]);

        // Format the date if provided
        $formattedDate = null;
        if (!empty($validated['date'])) {
            $formattedDate = \Carbon\Carbon::parse($validated['date'])->format('F j, Y');
        }

        // Generate PDF using Pandoc
        $pdfPath = $this->pdfGenerator->generate([
            'title' => $validated['title'],
            'author' => $validated['author'],
            'course' => $validated['course'] ?? null,
            'date' => $formattedDate,
            'rawContent' => $validated['content'],
        ]);

        // Generate a clean filename
        $filename = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $validated['title']) . '.pdf';

        return response()->download($pdfPath, $filename)->deleteFileAfterSend(true);
    }
}
