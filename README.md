# Audrey's Paper Generator

A Laravel application that converts markdown input into properly formatted Turabian-style academic papers with PDF export capability.

## Features

- **Markdown Input**: Write your paper content in familiar markdown syntax
- **Automatic Footnote Handling**: Use `[^1]` syntax for footnotes with automatic "Ibid." support
- **Block Quotation Detection**: Automatically formats quotes (3+ lines, 2+ sentences) according to Turabian guidelines
- **Professional PDF Output**: Generates properly formatted PDFs with:
  - Title page with centered content
  - 12pt Times New Roman font throughout
  - 1-inch margins on all sides
  - Double-spaced body text
  - Properly formatted footnotes
  - Correct page numbering

## Requirements

- PHP 8.2 or higher
- Composer
- Laravel 12.x

## Installation

1. **Clone the repository** (or copy the project files):
   ```bash
   cd turabian-generator
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Copy environment file and generate key**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Start the development server**:
   ```bash
   php artisan serve
   ```

5. **Access the application**:
   Open your browser and navigate to `http://localhost:8000`

## Usage

### Form Fields

- **Paper Title** (required): The title of your paper
- **Author Name** (required): Your name as it should appear on the paper
- **Course/Class Name** (optional): The course for which the paper is written
- **Date** (optional): The submission date
- **Paper Content** (required): Your paper content in markdown format

### Markdown Syntax

#### Basic Formatting
```markdown
**bold text**
*italic text*
# Heading 1
## Heading 2
### Heading 3
```

#### Lists
```markdown
- Unordered item
- Another item

1. Ordered item
2. Another item
```

#### Footnotes
```markdown
This statement requires a citation.[^1] This is another claim.[^2]

[^1]: John Smith, *Title of Book* (City: Publisher, Year), page.
[^2]: Jane Doe, "Article Title," *Journal Name* 10, no. 2 (Year): page.
```

**Note**: If you use the same citation consecutively, it will automatically be converted to "Ibid."

#### Block Quotations
```markdown
> This is a block quotation that spans multiple lines.
> Block quotations should be used for longer passages
> that you want to quote directly from a source.
```

Block quotations (3+ lines with 2+ sentences) are automatically:
- Single-spaced
- Indented 0.5 inches from the left margin

### Buttons

- **Download PDF**: Generates and downloads the formatted PDF file
- **Preview in Browser**: Opens the formatted paper in a new browser tab

## Turabian Formatting Specifications

This generator follows Turabian style guidelines:

### Title Page
- All text centered
- Double-spaced
- No page number displayed (counted as page i)

### First Page of Text
- Title in ALL CAPS, centered
- Page number at bottom center
- 1-inch margins

### Body Text
- 12pt Times New Roman
- Double-spaced
- First line of paragraphs indented 0.5 inches

### Footnotes
- Separated from text by a short horizontal line
- Full-sized numbers (not superscript) at start
- First line indented
- Single-spaced with blank line between each

### Block Quotations
- Single-spaced
- Indented 0.5 inches from left margin
- No quotation marks around the block

## Project Structure

```
turabian-generator/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── PaperController.php    # Main controller
│   └── Services/
│       └── TurabianParser.php         # Markdown parser
├── resources/
│   └── views/
│       └── paper/
│           ├── form.blade.php         # Input form
│           └── pdf.blade.php          # PDF template
├── routes/
│   └── web.php                        # Route definitions
├── example.md                         # Example markdown document
└── README.md                          # This file
```

## Dependencies

- [Laravel 12.x](https://laravel.com/) - PHP Framework
- [barryvdh/laravel-dompdf](https://github.com/barryvdh/laravel-dompdf) - PDF Generation
- [league/commonmark](https://commonmark.thephpleague.com/) - Markdown Parsing (included with Laravel)

## License

This project is open-sourced software.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.
