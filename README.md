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
- Pandoc (for PDF generation)
- LaTeX (TeX Live or MacTeX)

## System Installation

### Linux (Ubuntu/Debian)

```bash
# Install PHP and required extensions
sudo apt-get update
sudo apt-get install php php-cli php-mbstring php-xml php-curl php-zip unzip

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Pandoc
sudo apt-get install pandoc

# Install LaTeX (required for PDF generation)
sudo apt-get install texlive-latex-base texlive-latex-extra texlive-fonts-recommended
```

### Linux (Fedora/RHEL)

```bash
# Install PHP and required extensions
sudo dnf install php php-cli php-mbstring php-xml php-curl php-zip unzip

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Pandoc
sudo dnf install pandoc

# Install LaTeX
sudo dnf install texlive-scheme-basic texlive-collection-latexextra texlive-collection-fontsrecommended
```

### macOS

```bash
# Install Homebrew if not already installed
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install PHP
brew install php

# Install Composer
brew install composer

# Install Pandoc
brew install pandoc

# Install LaTeX (BasicTeX is smaller, MacTeX is full installation)
# Option 1: BasicTeX (smaller, ~100MB) - then install extra packages
brew install --cask basictex
# After installation, add to PATH and install required packages:
eval "$(/usr/libexec/path_helper)"
sudo tlmgr update --self
sudo tlmgr install collection-latexextra collection-fontsrecommended

# Option 2: MacTeX (full installation, ~4GB)
brew install --cask mactex
```

### Verify Installation

After installing the system dependencies, verify they're working:

```bash
php --version          # Should show PHP 8.2+
composer --version     # Should show Composer version
pandoc --version       # Should show Pandoc version
pdflatex --version     # Should show pdfTeX version
```

## Application Installation

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

4. **Create storage directories**:
   ```bash
   mkdir -p storage/app/temp
   chmod -R 775 storage
   ```

5. **Start the development server**:
   ```bash
   php artisan serve
   ```

6. **Access the application**:
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
│   │       └── PaperController.php      # Main controller
│   └── Services/
│       ├── TurabianParser.php           # Markdown parser
│       └── PandocPdfGenerator.php       # PDF generation via Pandoc
├── resources/
│   ├── templates/
│   │   └── turabian.latex               # LaTeX template for Turabian formatting
│   └── views/
│       └── paper/
│           ├── form.blade.php           # Input form
│           ├── print.blade.php          # Print-to-PDF template
│           └── pdf.blade.php            # Preview template
├── routes/
│   └── web.php                          # Route definitions
├── storage/
│   └── app/
│       └── temp/                        # Temporary files for PDF generation
└── README.md                            # This file
```

## Dependencies

- [Laravel 12.x](https://laravel.com/) - PHP Framework
- [Pandoc](https://pandoc.org/) - Universal document converter (system dependency)
- [TeX Live](https://tug.org/texlive/) / [MacTeX](https://tug.org/mactex/) - LaTeX distribution for PDF rendering
- [league/commonmark](https://commonmark.thephpleague.com/) - Markdown Parsing (included with Laravel)

## License

This project is open-sourced software.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.
