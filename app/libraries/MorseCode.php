<?php

namespace Fir\Libraries;

class MorseCode {

    /**
     * String a ser manipulada (texto em claro ou código morse)
     * @var string
     */
    private $string;

    /**
     * Construtor da classe. Recebe a string a ser codificada ou decodificada
     * @param string $string Texto de entrada
     */
    public function __construct($string) {
        $this->string = $string;
    }

    /**
     * Retorna a lista de caracteres disponíveis para conversão (letras minúsculas e espaço)
     *
     * @return  array
     */
    private static function getList() {
        return array(' ' => '/ ', 'a' => '.- ', 'b' => '-... ', 'c' => '-.-. ', 'd' => '-.. ', 'e' => '. ', 'f' => '..-. ', 'g' => '--. ', 'h' => '.... ', 'i' => '.. ', 'j' => '.--- ', 'k' => '-.- ', 'l' => '.-.. ', 'm' => '-- ', 'n' => '-. ', 'o' => '--- ', 'p' => '.--. ', 'q' => '--.- ', 'r' => '.-. ', 's' => '... ', 't' => '- ', 'u' => '..- ', 'v' => '...- ', 'w' => '.-- ', 'x' => '-.. ', 'y' => '-.-- ', 'z' => '--.. ');
    }

    /**
     * Codifica a string para código morse
     *
     * @return  string
     */
    public function encode() {
        // Converte a string para minúsculas e substitui cada caractere pelo equivalente em morse
        return str_replace(array_keys($this->getList()), $this->getList(), mb_strtolower($this->string));
    }

    /**
     * Decodifica uma string em código morse para texto
     *
     * @return  string
     */
    public function decode() {
        // Cria um array com os códigos morse (valores) e remove espaços extras
        $morse = array_map('trim', $this->getList());
        $output = '';
        // Separa a string em códigos morse e busca o caractere correspondente
        foreach(explode(' ', $this->string) as $value) {
            $output .= array_search($value, $morse);
        }
        // Retorna o texto decodificado em maiúsculas
        return mb_strtoupper($output);
    }
}