<?php

/**
 * Converte o número de visualizações para o formato K (mil), M (milhão), etc.
 *
 * @param   float   $value  O número a ser transformado
 * @return  array
 */
function formatViews($value) {
    // Se o número for menor que mil, retorna o valor original
    if(strlen($value) < 4) {
        return $value;
    }

    // Arredonda o valor para o inteiro mais próximo
    $number = round($value);

    // Formata o número com separador de milhar
    $number = number_format($number);
    $numberParts = explode(',', $number);

    // Calcula quantos grupos de milhar existem
    $numberPartsCount = count($numberParts) - 1;

    // Define os sufixos para mil, milhão, bilhão, trilhão
    $numberSection = ['k', 'm', 'b', 't'];

    // Monta a parte principal do número (ex: 1.2k)
    $count = $numberParts[0].((int)$numberParts[1][0] !== 0 ? '.'.$numberParts[1][0] : '');

    // Seleciona o sufixo adequado
    $decimals = $numberSection[$numberPartsCount - 1];

    // Retorna o valor formatado
    return ['count' => $count, 'decimals' => $decimals];
}

/**
 * Converte a duração de tempo para o formato 01:00:00, 01:00 ou 0:10
 *
 * @param   string  $value  A string a ser formatada
 * @return  string
 */
function formatDuration($value) {
    // Separa a string em partes (horas:minutos:segundos)
    $time = explode(':', $value);

    // Se horas e minutos forem zero, remove as horas e mostra minutos como dígito único
    if($time[0] == '00' && $time[1] == '00') {
        unset($time[0]);
        $time[1] = 0;
    } elseif($time[0] == '00') {
        // Se apenas as horas forem zero, remove as horas
        unset($time[0]);
    }

    // Retorna a string formatada
    return implode(':', $time);
}

/**
 * Formata a URL de uma imagem, podendo adicionar parâmetros para exibição dinâmica
 *
 * @param   string  $value  O valor a ser formatado (nome ou caminho da imagem)
 * @param   int     $type   Tipo de formatação (opcional)
 * @return  string
 */
function formatImageUrl($value, $type = null) {
    // Se o tipo for informado, monta a URL dinâmica para exibição da imagem
    if($type) {
        $value = URL_PATH.'/image.php?'.urlencode($value);
    }

    // Retorna a URL formatada
    return $value;
}