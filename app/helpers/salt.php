<?php

/**
 * Gera um salt (sequência aleatória de caracteres) para uso em segurança
 *
 * @param   int     $length     Tamanho do salt a ser gerado
 * @return  string
 */
function generateSalt($length = 10) {
    $salt = null;
    // Array com todos os caracteres possíveis (letras maiúsculas, minúsculas e números)
    $salt_chars = array_merge(range('A','Z'), range('a','z'), range(0,9));

    // Gera o salt sorteando caracteres aleatórios até atingir o tamanho desejado
    for($i = 0; $i < $length; $i++) {
        $salt .= $salt_chars[array_rand($salt_chars)];
    }

    // Retorna o salt gerado
    return $salt;
}