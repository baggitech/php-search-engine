<?php

/**
 * Procura por uma chave em um array multidimensional
 *
 * @param   string  $key        A chave a ser procurada
 * @param   array   $search     O array onde será feita a busca
 * @return  bool
 */
function array_key_exists_r($key, $search) {
    // Verifica se a chave existe no array atual
    $result = array_key_exists($key, $search);
    if($result) {
        // Se encontrou, retorna true
        return $result;
    }
    // Percorre todos os valores do array
    foreach($search as $v) {
        // Se o valor for um array, faz a busca recursiva
        if(is_array($v)) {
            $result = array_key_exists_r($key, $v);
        }
        // Se encontrou em algum nível, retorna true
        if($result) {
            return $result;
        }
    }
    // Se não encontrou, retorna false
    return $result;
}

/**
 * Procura por um valor em qualquer chave de um array multidimensional
 *
 * @param   string  $value      O valor a ser procurado
 * @param   array   $search     O array onde será feita a busca
 * @return  bool
 */
function array_value_exists_r($value, $search) {
    // Procura o valor no array atual
    $result = array_search($value, $search, true);
    if($result) {
        // Se encontrou, retorna a chave
        return $result;
    }
    // Percorre todos os valores do array
    foreach($search as $v) {
        // Se o valor for um array, faz a busca recursiva
        if(is_array($v)) {
            $result = array_value_exists_r($value, $v);
        }
        // Se encontrou em algum nível, retorna a chave
        if($result) {
            return $result;
        }
    }
    // Se não encontrou, retorna false
    return $result;
}