<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audrey's Paper Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .markdown-help code {
            background-color: #f3f4f6;
            padding: 0.125rem 0.375rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <header class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Audrey's Paper Generator</h1>
            <p class="text-gray-600">Convert your markdown content into a properly formatted Turabian-style academic paper</p>
        </header>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <strong class="font-bold">Please fix the following errors:</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('paper.generate') }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Title -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                        Paper Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter your paper title" required>
                </div>

                <!-- Author -->
                <div>
                    <label for="author" class="block text-sm font-medium text-gray-700 mb-1">
                        Author Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="author" id="author" value="{{ old('author', 'Audrey Hayden') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Your name" required>
                </div>

                <!-- Course -->
                <div>
                    <label for="course" class="block text-sm font-medium text-gray-700 mb-1">
                        Course/Class Name
                    </label>
                    <input type="text" name="course" id="course" value="{{ old('course') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="e.g., English 101">
                </div>

                <!-- Date -->
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-1">
                        Date
                    </label>
                    <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <!-- Markdown Content -->
            <div class="mb-6">
                <label for="content" class="block text-sm font-medium text-gray-700 mb-1">
                    Paper Content (Markdown) <span class="text-red-500">*</span>
                </label>
                <textarea name="content" id="content" rows="20"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm"
                          placeholder="Enter your paper content in markdown format..." required>{{ old('content') }}</textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="flex flex-col sm:flex-row gap-4">
                <button type="submit" formaction="{{ route('paper.download') }}"
                        class="flex-1 bg-green-600 text-white py-3 px-6 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors font-medium">
                    Download PDF
                </button>
                <button type="submit" formaction="{{ route('paper.preview') }}" formtarget="_blank"
                        class="flex-1 bg-gray-600 text-white py-3 px-6 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors font-medium">
                    Preview
                </button>
            </div>
        </form>

        <!-- Markdown Help Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-8 markdown-help">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Markdown Syntax Guide</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Basic Formatting</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li><code>**bold text**</code> - Bold text</li>
                        <li><code>*italic text*</code> - Italic text</li>
                        <li><code># Heading 1</code> - Main heading</li>
                        <li><code>## Heading 2</code> - Subheading</li>
                        <li><code>### Heading 3</code> - Sub-subheading</li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Lists</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li><code>- Item</code> - Unordered list</li>
                        <li><code>1. Item</code> - Ordered list</li>
                        <li><code>  - Nested</code> - Nested list (indent with spaces)</li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Footnotes</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li><code>[^1]</code> - Footnote reference in text</li>
                        <li><code>[^1]: Citation text</code> - Footnote definition (at end)</li>
                    </ul>
                    <p class="text-xs text-gray-500 mt-2">
                        Note: Consecutive identical footnotes automatically become "Ibid."
                    </p>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Block Quotations</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li><code>&gt; Quote text</code> - Block quotation</li>
                    </ul>
                    <p class="text-xs text-gray-500 mt-2">
                        Block quotes (3+ lines, 2+ sentences) are automatically formatted with proper Turabian indentation.
                    </p>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Bibliography</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li><code>[bib]: Entry text</code> - Single bibliography entry</li>
                    </ul>
                    <p class="text-xs text-gray-500 mt-2">
                        Use <code>*Title*</code> for italics in titles. Entries are automatically sorted alphabetically with hanging indent.
                    </p>
                </div>
            </div>

            <div class="mt-6 p-4 bg-gray-50 rounded-md">
                <h3 class="font-semibold text-gray-700 mb-2">Example with Footnotes and Bibliography</h3>
                <pre class="text-sm text-gray-600 whitespace-pre-wrap">The importance of proper citation cannot be overstated.[^1] Scholars have long debated this topic.[^2]

> This is a longer quotation that spans multiple lines.
> It demonstrates how block quotations should be formatted
> in academic papers following Turabian guidelines.

[^1]: John Smith, *Academic Writing Guide* (New York: Publisher, 2023), 45.
[^2]: Jane Doe, "Citation Practices," *Journal of Academia* 15, no. 3 (2022): 112.

[bib]: Doe, Jane. "Citation Practices." *Journal of Academia* 15, no. 3 (2022): 110-125.
[bib]: Smith, John. *Academic Writing Guide*. New York: Publisher, 2023.</pre>
            </div>
        </div>

        <footer class="text-center mt-8 text-sm text-gray-500">
            <p>Audrey's Paper Generator - Formats papers according to Turabian style guidelines</p>
        </footer>
    </div>
</body>
</html>
