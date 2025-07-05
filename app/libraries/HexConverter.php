<?php

namespace Fir\Libraries;

class HexConverter {
    /**
     * @var string Armazena o valor hexadecimal da cor
     */
    private $hex;

    /**
     * Construtor da classe. Aceita cor em formato hexadecimal (3 ou 6 dígitos)
     * Se for 3 dígitos, converte para 6 dígitos duplicando cada caractere
     *
     * @param string $hex Cor em formato hexadecimal
     */
    public function __construct($hex) {
        if(strlen($hex) == 3) {
            // Converte formato #abc para #aabbcc
            $this->hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        } else {
            $this->hex = $hex;
        }
    }

    /**
     * Retorna a cor hexadecimal em letras maiúsculas
     *
     * @return  string
     */
    public function hex() {
        return mb_strtoupper($this->hex);
    }

    /**
     * Converte a cor hexadecimal para RGB
     *
     * @return  array
     */
    public function rgb() {
        if(strlen($this->hex) == 3) {
            // Para formato #abc
            $r = hexdec(substr($this->hex,0,1).substr($this->hex,0,1));
            $g = hexdec(substr($this->hex,1,1).substr($this->hex,1,1));
            $b = hexdec(substr($this->hex,2,1).substr($this->hex,2,1));
        } else {
            // Para formato #aabbcc
            $r = hexdec(substr($this->hex,0,2));
            $g = hexdec(substr($this->hex,2,2));
            $b = hexdec(substr($this->hex,4,2));
        }
        // Retorna array com valores RGB
        return array($r, $g, $b);
    }

    /**
     * Converte a cor hexadecimal para HSL
     *
     * @return  array
     */
    public function hsl() {
        // Separa os componentes RGB normalizados (0-1)
        $color = array($this->hex[0].$this->hex[1], $this->hex[2].$this->hex[3], $this->hex[4].$this->hex[5]);
        $rgb = array_map(function($part) {
            return hexdec($part) / 255;
        }, $color);

        $max = max($rgb);
        $min = min($rgb);
        $l = ($max+$min)/2;

        if($max == $min) {
            // Cor acromática (sem saturação)
            $h = $s = 0;
        } else {
            $d = $max-$min;
            // Calcula saturação
            $s = $l > 0.5 ? $d/(2-$max-$min) : $d/($max+$min);
            // Calcula matiz
            switch($max) {
                case $rgb[0]:
                    $h = ($rgb[1]-$rgb[2])/$d+($rgb[1] < $rgb[2] ? 6 : 0);
                    break;
                case $rgb[1]:
                    $h = ($rgb[2]-$rgb[0])/$d+2;
                    break;
                case $rgb[2]:
                    $h = ($rgb[0]-$rgb[1])/$d+4;
                    break;
            }
            $h *= 60;
        }
        // Retorna array com valores HSL arredondados
        return array(round($h), round($s*100), round($l*100));
    }

    /**
     * Converte a cor hexadecimal para CMYK
     *
     * @return  array
     */
    public function cmyk() {
        // Converte para RGB primeiro
        $rgb = $this->rgb();
        // Calcula os componentes Ciano, Magenta e Amarelo
        $cyan = 255-$rgb[0];
        $magenta = 255-$rgb[1];
        $yellow = 255-$rgb[2];
        // Calcula o componente Preto
        $black = min($cyan, $magenta, $yellow);
        // Normaliza os valores para porcentagem
        $cyan = (((($cyan-$black)/(255-$black))*255)/255)*100;
        $magenta = (((($magenta-$black)/(255-$black))*255)/255)*100;
        $yellow = (((($yellow-$black)/(255-$black))*255)/255)*100;
        $black = ($black/255)*100;
        // Retorna array com valores CMYK arredondados
        return array(round($cyan), round($magenta), round($yellow), round($black));
    }
}