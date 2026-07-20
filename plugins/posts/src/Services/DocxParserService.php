<?php

namespace Plugins\Posts\Services;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class DocxParserService
{
    /**
     * The extracted post title for generating placeholder metadata.
     */
    protected string $postTitle = '';

    /**
     * Counter for extracted images to generate sequential alt tags.
     */
    protected int $imageCount = 0;

    /**
     * Flags whether to downlevel subheaders.
     */
    protected bool $shouldDownlevel = false;

    /**
     * Parse a DOCX file and convert it to an array containing HTML content,
     * extracted title, and metadata.
     *
     * @param  string  $filePath  Path to the .docx file
     * @return array{title: string, content: string}
     */
    public function parse(string $filePath): array
    {
        $zip = new ZipArchive;
        if ($zip->open($filePath) !== true) {
            throw new \RuntimeException('Failed to open DOCX file.');
        }

        // 1. Read relationships (for images and hyperlinks)
        $relations = $this->readRelationships($zip);

        // 2. Read document content XML
        $contentXml = $zip->getFromName('word/document.xml');
        if (! $contentXml) {
            $zip->close();
            throw new \RuntimeException('Invalid DOCX: main document part is missing.');
        }

        // 3. Parse XML using DOMDocument & XPath
        $dom = new DOMDocument;
        // Prevent XML entity injection and warnings
        libxml_use_internal_errors(true);
        $dom->loadXML($contentXml);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $xpath->registerNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
        $xpath->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
        $xpath->registerNamespace('pic', 'http://schemas.openxmlformats.org/drawingml/2006/picture');
        $xpath->registerNamespace('wp', 'http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing');

        // Extract title: search for first Heading 1
        $this->postTitle = $this->extractTitle($xpath);
        $this->imageCount = 0;

        // Determine if we should downlevel all headings (if more than one Heading 1 in the document)
        $heading1Nodes = $xpath->query('//w:p[w:pPr/w:pStyle[@w:val="Heading1"]]');
        $this->shouldDownlevel = ($heading1Nodes && $heading1Nodes->length > 1);

        // Parse HTML body
        $htmlContent = $this->parseBody($xpath, $zip, $relations);

        $zip->close();

        return [
            'title' => $this->postTitle ?: 'Imported Post',
            'content' => $htmlContent,
        ];
    }

    /**
     * Map relationship IDs to their target media paths and hyperlinks.
     */
    protected function readRelationships(ZipArchive $zip): array
    {
        $relations = [];
        $relsXml = $zip->getFromName('word/_rels/document.xml.rels');
        if (! $relsXml) {
            return $relations;
        }

        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadXML($relsXml);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('rel', 'http://schemas.openxmlformats.org/package/2006/relationships');

        $nodes = $xpath->query('//rel:Relationship');
        foreach ($nodes as $node) {
            $id = $node->getAttribute('Id');
            $target = $node->getAttribute('Target');
            $type = $node->getAttribute('Type');

            if (str_contains($type, '/image')) {
                // Targets are relative to the word folder (e.g. media/image1.png)
                $relations[$id] = [
                    'type' => 'image',
                    'target' => 'word/'.ltrim($target, '/'),
                ];
            } elseif (str_contains($type, '/hyperlink')) {
                $relations[$id] = [
                    'type' => 'hyperlink',
                    'target' => $target,
                ];
            }
        }

        return $relations;
    }

    /**
     * Extract the main heading or first paragraph as a title candidate.
     */
    protected function extractTitle(DOMXPath $xpath): string
    {
        // Try Heading 1 first
        $headingNode = $xpath->query('//w:p[w:pPr/w:pStyle[@w:val="Heading1"]]')->item(0);
        if ($headingNode) {
            return trim($headingNode->textContent);
        }

        // Fallback to first non-empty paragraph
        $pNodes = $xpath->query('//w:p');
        foreach ($pNodes as $node) {
            $text = trim($node->textContent);
            if (! empty($text)) {
                return Str::limit($text, 100);
            }
        }

        return '';
    }

    /**
     * Convert the XML tree elements under w:body to HTML.
     */
    protected function parseBody(DOMXPath $xpath, ZipArchive $zip, array $relations): string
    {
        $bodyNode = $xpath->query('//w:body')->item(0);
        if (! $bodyNode) {
            return '';
        }

        $html = '';
        $inList = false;
        $listType = 'ul'; // ul or ol

        foreach ($bodyNode->childNodes as $node) {
            if ($node->nodeName === 'w:p') {
                // Check if this paragraph is part of a list
                $isListItem = $xpath->query('w:pPr/w:numPr', $node)->length > 0;

                if ($isListItem) {
                    if (! $inList) {
                        // Check list type
                        $numId = $xpath->query('w:pPr/w:numPr/w:numId', $node)->item(0)?->getAttribute('w:val');
                        $listType = ($numId === '2' || $numId === '4') ? 'ol' : 'ul'; // simple heuristic
                        $html .= "<{$listType}>\n";
                        $inList = true;
                    }
                    $html .= '  <li>'.$this->parseParagraphRuns($node, $xpath, $zip, $relations)."</li>\n";
                } else {
                    if ($inList) {
                        $html .= "</{$listType}>\n";
                        $inList = false;
                    }

                    // Check Heading style
                    $style = $xpath->query('w:pPr/w:pStyle', $node)->item(0)?->getAttribute('w:val');
                    if ($style && preg_match('/Heading([1-6])/', $style, $matches)) {
                        $level = (int) $matches[1];

                        // Downlevel headers if more than one 'Header 1' on the page
                        if ($this->shouldDownlevel) {
                            $level = min(6, $level + 1);
                        } else {
                            // Standard conversion: convert h1 to h2 in blog post body
                            $level = $level === 1 ? 2 : $level;
                        }

                        $html .= "<h{$level}>".$this->parseParagraphRuns($node, $xpath, $zip, $relations)."</h{$level}>\n";
                    } else {
                        $pText = $this->parseParagraphRuns($node, $xpath, $zip, $relations);
                        if (! empty($pText) || $xpath->query('.//w:drawing', $node)->length > 0) {
                            $html .= "<p>{$pText}</p>\n";
                        }
                    }
                }
            } elseif ($node->nodeName === 'w:tbl') {
                if ($inList) {
                    $html .= "</{$listType}>\n";
                    $inList = false;
                }
                $html .= $this->parseTable($node, $xpath, $zip, $relations);
            }
        }

        if ($inList) {
            $html .= "</{$listType}>\n";
        }

        return $html;
    }

    /**
     * Parse paragraph text runs, applying styles (bold, italic, images, hyperlinks, etc.).
     */
    protected function parseParagraphRuns(\DOMNode $pNode, DOMXPath $xpath, ZipArchive $zip, array $relations): string
    {
        $text = '';

        // Find text runs (<w:r>), drawings, or hyperlinks inside the paragraph
        $children = $xpath->query('w:r|w:drawing|w:hyperlink', $pNode);

        foreach ($children as $child) {
            if ($child->nodeName === 'w:r') {
                $runText = '';
                $tNodes = $xpath->query('w:t', $child);
                foreach ($tNodes as $tNode) {
                    $runText .= htmlspecialchars($tNode->textContent);
                }

                // Check formatting
                $isBold = $xpath->query('w:rPr/w:b', $child)->length > 0;
                $isItalic = $xpath->query('w:rPr/w:i', $child)->length > 0;
                $isUnderline = $xpath->query('w:rPr/w:u', $child)->length > 0;
                $isStrike = $xpath->query('w:rPr/w:strike', $child)->length > 0;

                if ($isBold) {
                    $runText = "<strong>{$runText}</strong>";
                }
                if ($isItalic) {
                    $runText = "<em>{$runText}</em>";
                }
                if ($isUnderline) {
                    $runText = "<u>{$runText}</u>";
                }
                if ($isStrike) {
                    $runText = "<del>{$runText}</del>";
                }

                // Check if the run itself contains a drawing/image
                $drawNode = $xpath->query('.//w:drawing', $child)->item(0);
                if ($drawNode) {
                    $runText .= $this->parseDrawing($drawNode, $xpath, $zip, $relations);
                }

                $text .= $runText;
            } elseif ($child->nodeName === 'w:drawing') {
                $text .= $this->parseDrawing($child, $xpath, $zip, $relations);
            } elseif ($child->nodeName === 'w:hyperlink') {
                $rId = $child->getAttribute('r:id');
                $url = '';
                if ($rId && isset($relations[$rId]) && $relations[$rId]['type'] === 'hyperlink') {
                    $url = $relations[$rId]['target'];
                }

                // Convert link contents
                $linkText = $this->parseParagraphRuns($child, $xpath, $zip, $relations);

                if (! empty($linkText)) {
                    if (! empty($url)) {
                        // Advanced content corrections: clean URL site prefix using domain parsing
                        $siteUrlParts = parse_url(url('/'));
                        $urlParts = parse_url($url);
                        $isLocal = false;

                        if (empty($urlParts['host'])) {
                            $isLocal = true;
                        } elseif (isset($urlParts['host'], $siteUrlParts['host'])) {
                            if (strtolower($urlParts['host']) === strtolower($siteUrlParts['host'])) {
                                $isLocal = true;
                            } elseif (strtolower($urlParts['host']) === 'localhost') {
                                $isLocal = true;
                            }
                        }

                        if ($isLocal) {
                            $path = $urlParts['path'] ?? '/';
                            $query = isset($urlParts['query']) ? '?'.$urlParts['query'] : '';
                            $fragment = isset($urlParts['fragment']) ? '#'.$urlParts['fragment'] : '';
                            $url = '/'.ltrim($path, '/').$query.$fragment;
                        }

                        // Determine if it is external link
                        $isExternal = ! str_starts_with($url, '/');
                        $targetAttr = $isExternal ? ' target="_blank" rel="noopener noreferrer"' : '';

                        // Render clean a tag (strips extra word link attributes)
                        $text .= '<a href="'.htmlspecialchars($url).'"'.$targetAttr.'>'.$linkText.'</a>';
                    } else {
                        $text .= $linkText;
                    }
                }
            }
        }

        return $text;
    }

    /**
     * Extract image from inline drawing, store it, and return img tag.
     */
    protected function parseDrawing(\DOMNode $drawingNode, DOMXPath $xpath, ZipArchive $zip, array $relations): string
    {
        $blipNodes = $xpath->query('.//a:blip', $drawingNode);
        $embedNode = ($blipNodes && $blipNodes->length > 0) ? $blipNodes->item(0) : null;
        if (! $embedNode) {
            // GA/Office relationship mapping fallback
            $fallbackNodes = $xpath->query('.//*[@r:embed]', $drawingNode);
            $embedNode = ($fallbackNodes && $fallbackNodes->length > 0) ? $fallbackNodes->item(0) : null;
        }

        if (! $embedNode) {
            return '';
        }

        $rId = $embedNode->getAttribute('r:embed');
        if (empty($rId) || ! isset($relations[$rId]) || $relations[$rId]['type'] !== 'image') {
            return '';
        }

        $zipPath = $relations[$rId]['target'];
        $binary = $zip->getFromName($zipPath);

        if (! $binary) {
            return '';
        }

        // Determine file extension
        $ext = pathinfo($zipPath, PATHINFO_EXTENSION) ?: 'png';
        $filename = 'post_'.uniqid().'.'.$ext;
        $savePath = 'uploads/posts/'.$filename;

        // Store to public storage disk
        Storage::disk('public')->put($savePath, $binary);
        $url = asset('storage/'.$savePath);

        // Fetch original Alt Text (descr) & Title (title) from docPr
        $docPrNode = $xpath->query('.//wp:docPr', $drawingNode)->item(0);
        $alt = '';
        $title = '';
        if ($docPrNode) {
            $alt = trim($docPrNode->getAttribute('descr'));
            $title = trim($docPrNode->getAttribute('title'));
        }

        // Clean unneeded garbage elements (like Picture 1, file extensions)
        if (preg_match('/^(Picture|Image|Grafik|Object)\s*\d+$/i', $alt) || str_ends_with(strtolower($alt), '.png') || str_ends_with(strtolower($alt), '.jpg')) {
            $alt = '';
        }
        if (preg_match('/^(Picture|Image|Grafik|Object)\s*\d+$/i', $title) || str_ends_with(strtolower($title), '.png') || str_ends_with(strtolower($title), '.jpg')) {
            $title = '';
        }

        // Generate dynamic description attributes if they are empty
        $this->imageCount++;
        $postTitleClean = $this->postTitle ?: 'Imported Post';
        if (empty($alt)) {
            $alt = "{$postTitleClean} - Image {$this->imageCount}";
        }
        if (empty($title)) {
            $title = "{$postTitleClean} - Image {$this->imageCount}";
        }

        return '<img src="'.$url.'" alt="'.htmlspecialchars($alt).'" title="'.htmlspecialchars($title).'" class="post-inline-image" />';
    }

    /**
     * Convert tables in docx to HTML.
     */
    protected function parseTable(\DOMNode $tableNode, DOMXPath $xpath, ZipArchive $zip, array $relations): string
    {
        $html = "<table class=\"post-table\">\n";
        $trNodes = $xpath->query('w:tr', $tableNode);

        foreach ($trNodes as $tr) {
            $html .= "  <tr>\n";
            $tcNodes = $xpath->query('w:tc', $tr);
            foreach ($tcNodes as $tc) {
                // Get cell text by parsing paragraphs inside it
                $cellText = '';
                $pNodes = $xpath->query('w:p', $tc);
                foreach ($pNodes as $p) {
                    $cellText .= $this->parseParagraphRuns($p, $xpath, $zip, $relations).'<br>';
                }
                $cellText = rtrim($cellText, '<br>');
                $html .= "    <td>{$cellText}</td>\n";
            }
            $html .= "  </tr>\n";
        }
        $html .= "</table>\n";

        return $html;
    }
}
