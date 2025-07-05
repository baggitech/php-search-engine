<?php

/**
 * Converte caracteres em entidades HTML
 *
 * @param   string  $value  A string a ser escapada
 * @return  string
 */
function e($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Limita a quantidade de caracteres em uma string e fornece um trecho dela
 *
 * @param   string  $value      String de origem
 * @param   int     $start      Posição inicial (começando do 0)
 * @param   int     $length     Número máximo de caracteres
 * @param   array   $options    String a ser adicionada ao final (ex: reticências)
 * @return  string
 */
function str_limit($value, $start = 0, $length = null, $options = []) {
    $result = mb_substr($value, $start, $length);

    // Se a string original for maior que o limite e o limite não for nulo
    if(mb_strlen($value) > $length && is_null($length) == false) {
        if(isset($options['ellipsis'])) {
            // Adiciona reticências ou outro sufixo
            $result = $result.$options['ellipsis'];
        }
    }

    return $result;
}

/**
 * Trunca textos.
 *
 * Corta uma string para o tamanho desejado e adiciona reticências se necessário.
 *
 * Opções:
 * - 'ellipsis': Sufixo a ser adicionado ao final (ex: ...)
 * - 'exact': Se false, não corta palavras ao meio
 * - 'html': Se true, trata corretamente tags HTML
 *
 * @param   string  $text       Texto a ser truncado
 * @param   int     $length     Tamanho máximo (incluindo reticências)
 * @param   array   $options    Opções adicionais
 * @return  string
 */
function truncate($text, $length = 100, $options = []) {
    $default = ['ellipsis' => '...', 'exact' => true, 'html' => false];
    $options += $default;
    $prefix = '';
    $suffix = $options['ellipsis'];

    if($options['html']) {
        // Calcula o tamanho das reticências sem tags HTML
        $ellipsisLength = mb_strlen(strip_tags($options['ellipsis']));
        $truncateLength = 0;
        $totalLength = 0;
        $openTags = [];
        $truncate = '';

        // Percorre o texto identificando tags HTML e conteúdo
        preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);
        foreach($tags as $tag) {
            $contentLength = 0;
            $defaultHtmlNoCount = ['style', 'script'];
            if (!in_array($tag[2], $defaultHtmlNoCount, true)) {
                $contentLength = mb_strlen($tag[3]);
            }

            if($truncate === '') {
                // Gerencia tags abertas e fechadas
                if(!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/i', $tag[2])) {
                    if(preg_match('/<[\w]+[^>]*>/', $tag[0])) {
                        array_unshift($openTags, $tag[2]);
                    } elseif(preg_match('/<\/([\w]+)[^>]*>/', $tag[0], $closeTag)) {
                        $pos = array_search($closeTag[1], $openTags);
                        if($pos !== false) {
                            array_splice($openTags, $pos, 1);
                        }
                    }
                }
                $prefix .= $tag[1];
                // Verifica se atingiu o limite de caracteres
                if($totalLength + $contentLength + $ellipsisLength > $length) {
                    $truncate = $tag[3];
                    $truncateLength = $length - $totalLength;
                } else {
                    $prefix .= $tag[3];
                }
            }
            $totalLength += $contentLength;
            if($totalLength > $length) {
                break;
            }
        }
        // Se o texto for menor que o limite, retorna o texto original
        if($totalLength <= $length) {
            return $text;
        }
        $text = $truncate;
        $length = $truncateLength;
        // Fecha as tags HTML abertas
        foreach($openTags as $tag) {
            $suffix .= '</'.$tag.'>';
        }
    } else {
        // Se o texto for menor que o limite, retorna o texto original
        if(mb_strlen($text) <= $length) {
            return $text;
        }
        $ellipsisLength = mb_strlen($options['ellipsis']);
    }
    // Corta o texto no limite definido
    $result = mb_substr($text, 0, $length - $ellipsisLength);
    if(!$options['exact']) {
        // Se não for corte exato, remove a última palavra para não cortar no meio
        if(mb_substr($text, $length - $ellipsisLength, 1) !== ' ') {
            $result = removeLastWord($result);
        }
        // Se o resultado ficar vazio, corta sem considerar as reticências
        if(!strlen($result)) {
            $result = mb_substr($text, 0, $length);
        }
    }
    return $prefix.$result.$suffix;
}

/**
 * Remove a última palavra do texto informado
 *
 * @param   string  $text   Texto de entrada
 * @return  string
 */
function removeLastWord($text) {
    $spacePos = mb_strrpos($text, ' ');
    if($spacePos !== false) {
        $lastWord = mb_strrpos($text, $spacePos);
        // Para idiomas sem separação de palavras, verifica se é uma palavra
        if(mb_strwidth($lastWord) === mb_strlen($lastWord)) {
            $text = mb_substr($text, 0, $spacePos);
        }
        return $text;
    }
    return '';
}