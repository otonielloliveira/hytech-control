<?php

namespace App\Helpers;

class HtmlHelper
{
    /**
     * Sanitiza HTML permitindo apenas tags seguras
     */
    public static function sanitize(?string $html): ?string
    {
        if (!$html) {
            return null;
        }

        // Tags permitidas para conteúdo de blog/palestras
        $allowedTags = [
            'p', 'br', 'strong', 'b', 'em', 'i', 'u', 'ul', 'ol', 'li',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote', 'a', 'img',
            'div', 'span', 'code', 'pre'
        ];

        // Atributos permitidos
        $allowedAttributes = [
            'href', 'src', 'alt', 'title', 'class', 'id', 'style'
        ];

        return strip_tags($html, '<' . implode('><', $allowedTags) . '>');
    }

    /**
     * Converte texto plano em HTML com quebras de linha
     */
    public static function textToHtml(?string $text): ?string
    {
        if (!$text) {
            return null;
        }

        return nl2br(e($text));
    }

    /**
     * Processa conteúdo automaticamente (HTML, Markdown ou texto)
     */
    public static function processContent(?string $content): ?string
    {
        if (!$content) {
            return null;
        }

        // Se contém marcadores de Markdown
        if (preg_match('/[*_#\[\]`]/', $content)) {
            return self::parseMarkdown($content);
        }

        // Se já contém HTML, sanitiza
        if (strip_tags($content) !== $content) {
            return self::sanitize($content);
        }

        // Se é texto plano, converte para HTML
        return self::textToHtml($content);
    }

    /**
     * Converte Markdown básico para HTML
     */
    public static function parseMarkdown(?string $markdown): ?string
    {
        if (!$markdown) {
            return null;
        }

        // Conversões básicas de Markdown
        $html = $markdown;
        
        // Headers
        $html = preg_replace('/^### (.*$)/m', '<h3>$1</h3>', $html);
        $html = preg_replace('/^## (.*$)/m', '<h2>$1</h2>', $html);
        $html = preg_replace('/^# (.*$)/m', '<h1>$1</h1>', $html);
        
        // Bold e Italic
        $html = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $html);
        $html = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $html);
        $html = preg_replace('/__(.*?)__/', '<strong>$1</strong>', $html);
        $html = preg_replace('/_(.*?)_/', '<em>$1</em>', $html);
        
        // Strike
        $html = preg_replace('/~~(.*?)~~/', '<del>$1</del>', $html);
        
        // Links
        $html = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2">$1</a>', $html);
        
        // Listas
        $html = preg_replace('/^\* (.*)$/m', '<li>$1</li>', $html);
        $html = preg_replace('/^\d+\. (.*)$/m', '<li>$1</li>', $html);
        
        // Wrap listas
        $html = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $html);
        
        // Quebras de linha
        $html = nl2br($html);
        
        return $html;
    }
}