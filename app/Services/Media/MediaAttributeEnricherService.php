<?php

namespace App\Services\Media;

use Illuminate\Support\Str;

class MediaAttributeEnricherService
{
    /**
     * Common document extensions
     */
    protected static array $documentExtensions = [
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'csv', 'txt', 'rtf', 'zip', 'rar', '7z', 'tar', 'gz'
    ];

    /**
     * Common media extensions
     */
    protected static array $mediaExtensions = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico', 'avif',
        'mp4', 'webm', 'ogg', 'mp3', 'wav', 'avi', 'mov', 'wmv', 'flv', 'mkv',
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'csv', 'zip', 'rar', '7z', 'tar', 'gz'
    ];

    /**
     * Check if a given URL is a media or document file (excluding it from standard HTML web page links)
     */
    public static function isMediaUrl(?string $url): bool
    {
        if (empty($url)) {
            return false;
        }

        $cleanUrl = trim(explode('?', explode('#', $url)[0])[0]);
        $extension = strtolower(pathinfo($cleanUrl, PATHINFO_EXTENSION));

        if (in_array($extension, self::$mediaExtensions, true)) {
            return true;
        }

        if (str_contains($url, '/storage/uploads/') || str_contains($url, '/storage/media/') || str_contains($url, '/storage/documents/')) {
            return true;
        }

        return false;
    }

    /**
     * Check if URL is specifically a downloadable document
     */
    public static function isDocumentUrl(?string $url): bool
    {
        if (empty($url)) {
            return false;
        }

        $cleanUrl = trim(explode('?', explode('#', $url)[0])[0]);
        $extension = strtolower(pathinfo($cleanUrl, PATHINFO_EXTENSION));

        return in_array($extension, self::$documentExtensions, true);
    }

    /**
     * Enrich all media tags in HTML content with complete, SEO-friendly, and accessible attributes
     */
    public static function enrich(string $html, string $fallbackTitle = 'Article Media'): string
    {
        if (empty(trim($html))) {
            return '';
        }

        // 1. Enrich <img> tags
        $html = preg_replace_callback('/<img\s+([^>]*?)>/is', function ($matches) use ($fallbackTitle) {
            $attrs = $matches[1];

            // Extract src
            preg_match('/src=["\']([^"\']*)["\']/i', $attrs, $srcMatch);
            $src = $srcMatch[1] ?? '';
            $filename = !empty($src) ? pathinfo(parse_url($src, PHP_URL_PATH) ?? '', PATHINFO_FILENAME) : '';
            $filenameAlt = !empty($filename) ? ucwords(str_replace(['-', '_', '.'], ' ', $filename)) : '';

            // Extract existing alt
            $hasAlt = preg_match('/alt=["\']([^"\']*)["\']/i', $attrs, $altMatch);
            $altValue = $hasAlt ? trim($altMatch[1]) : '';

            if (empty($altValue)) {
                $altValue = !empty($filenameAlt) ? $filenameAlt : $fallbackTitle;
                if ($hasAlt) {
                    $attrs = preg_replace('/alt=["\'][^"\']*["\']/i', 'alt="' . htmlspecialchars($altValue, ENT_QUOTES, 'UTF-8') . '"', $attrs);
                } else {
                    $attrs .= ' alt="' . htmlspecialchars($altValue, ENT_QUOTES, 'UTF-8') . '"';
                }
            }

            // Ensure title
            if (!preg_match('/title=["\'][^"\']*["\']/i', $attrs)) {
                $attrs .= ' title="' . htmlspecialchars($altValue, ENT_QUOTES, 'UTF-8') . '"';
            }

            // Ensure loading="lazy"
            if (!preg_match('/loading=["\'][^"\']*["\']/i', $attrs)) {
                $attrs .= ' loading="lazy"';
            }

            // Ensure decoding="async"
            if (!preg_match('/decoding=["\'][^"\']*["\']/i', $attrs)) {
                $attrs .= ' decoding="async"';
            }

            // Ensure class includes img-fluid
            if (preg_match('/class=["\']([^"\']*)["\']/i', $attrs, $classMatch)) {
                $existingClass = $classMatch[1];
                if (!str_contains($existingClass, 'img-fluid')) {
                    $newClass = trim($existingClass . ' img-fluid rounded-4 shadow-sm');
                    $attrs = preg_replace('/class=["\'][^"\']*["\']/i', 'class="' . $newClass . '"', $attrs);
                }
            } else {
                $attrs .= ' class="img-fluid rounded-4 shadow-sm my-3"';
            }

            return '<img ' . trim($attrs) . '>';
        }, $html);

        // 2. Enrich <video> tags
        $html = preg_replace_callback('/<video\s+([^>]*?)>/is', function ($matches) use ($fallbackTitle) {
            $attrs = $matches[1];

            if (!preg_match('/controls/i', $attrs)) {
                $attrs .= ' controls';
            }
            if (!preg_match('/preload=["\'][^"\']*["\']/i', $attrs)) {
                $attrs .= ' preload="metadata"';
            }
            if (!preg_match('/playsinline/i', $attrs)) {
                $attrs .= ' playsinline';
            }
            if (!preg_match('/title=["\'][^"\']*["\']/i', $attrs)) {
                $attrs .= ' title="' . htmlspecialchars($fallbackTitle . ' Video', ENT_QUOTES, 'UTF-8') . '"';
            }
            if (!preg_match('/aria-label=["\'][^"\']*["\']/i', $attrs)) {
                $attrs .= ' aria-label="Video player for ' . htmlspecialchars($fallbackTitle, ENT_QUOTES, 'UTF-8') . '"';
            }

            return '<video ' . trim($attrs) . '>';
        }, $html);

        // 3. Enrich <audio> tags
        $html = preg_replace_callback('/<audio\s+([^>]*?)>/is', function ($matches) use ($fallbackTitle) {
            $attrs = $matches[1];

            if (!preg_match('/controls/i', $attrs)) {
                $attrs .= ' controls';
            }
            if (!preg_match('/preload=["\'][^"\']*["\']/i', $attrs)) {
                $attrs .= ' preload="metadata"';
            }
            if (!preg_match('/title=["\'][^"\']*["\']/i', $attrs)) {
                $attrs .= ' title="' . htmlspecialchars($fallbackTitle . ' Audio', ENT_QUOTES, 'UTF-8') . '"';
            }

            return '<audio ' . trim($attrs) . '>';
        }, $html);

        // 4. Enrich video <iframe> embeds (YouTube, Vimeo, etc.)
        $html = preg_replace_callback('/<iframe\s+([^>]*?)>(.*?)<\/iframe>/is', function ($matches) use ($fallbackTitle) {
            $attrs = $matches[1];
            $inner = $matches[2];

            // Ensure title
            if (!preg_match('/title=["\'][^"\']*["\']/i', $attrs)) {
                $attrs .= ' title="' . htmlspecialchars($fallbackTitle . ' Video Presentation', ENT_QUOTES, 'UTF-8') . '"';
            }

            // Ensure loading="lazy"
            if (!preg_match('/loading=["\'][^"\']*["\']/i', $attrs)) {
                $attrs .= ' loading="lazy"';
            }

            // Ensure allow attributes for video embeds
            if (!preg_match('/allow=["\'][^"\']*["\']/i', $attrs)) {
                $attrs .= ' allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"';
            }

            // Ensure allowfullscreen
            if (!preg_match('/allowfullscreen/i', $attrs)) {
                $attrs .= ' allowfullscreen';
            }

            $iframeTag = '<iframe ' . trim($attrs) . '>' . $inner . '</iframe>';

            return $iframeTag;
        }, $html);

        // 5. Enrich Document & Downloadable File Links <a> (PDF, DOCX, ZIP, etc.)
        $html = preg_replace_callback('/<a\s+([^>]*?)>(.*?)<\/a>/is', function ($matches) {
            $attrs = $matches[1];
            $anchorContent = $matches[2];

            preg_match('/href=["\']([^"\']*)["\']/i', $attrs, $hrefMatch);
            $href = $hrefMatch[1] ?? '';

            if (self::isDocumentUrl($href)) {
                $cleanUrl = trim(explode('?', explode('#', $href)[0])[0]);
                $extension = strtoupper(pathinfo($cleanUrl, PATHINFO_EXTENSION));
                $filename = basename($cleanUrl);

                // Add target="_blank"
                if (!preg_match('/target=["\'][^"\']*["\']/i', $attrs)) {
                    $attrs .= ' target="_blank"';
                }

                // Add rel="noopener noreferrer"
                if (preg_match('/rel=["\']([^"\']*)["\']/i', $attrs, $relMatch)) {
                    $relVal = $relMatch[1];
                    if (!str_contains($relVal, 'noopener')) {
                        $relVal .= ' noopener noreferrer';
                        $attrs = preg_replace('/rel=["\'][^"\']*["\']/i', 'rel="' . trim($relVal) . '"', $attrs);
                    }
                } else {
                    $attrs .= ' rel="noopener noreferrer"';
                }

                // Add download attribute if not already present
                if (!preg_match('/download/i', $attrs)) {
                    $attrs .= ' download';
                }

                // Add title & aria-label
                if (!preg_match('/title=["\'][^"\']*["\']/i', $attrs)) {
                    $attrs .= ' title="Download ' . htmlspecialchars($filename, ENT_QUOTES, 'UTF-8') . '"';
                }
                if (!preg_match('/aria-label=["\'][^"\']*["\']/i', $attrs)) {
                    $attrs .= ' aria-label="Download ' . htmlspecialchars($filename, ENT_QUOTES, 'UTF-8') . ' (' . $extension . ' document)"';
                }

                // Add data-doc-type
                if (!preg_match('/data-doc-type/i', $attrs)) {
                    $attrs .= ' data-doc-type="' . strtolower($extension) . '"';
                }
            }

            return '<a ' . trim($attrs) . '>' . $anchorContent . '</a>';
        }, $html);

        return $html;
    }
}
