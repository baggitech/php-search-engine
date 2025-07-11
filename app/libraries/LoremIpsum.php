<?php

namespace Fir\Libraries;

class LoremIpsum {
    /**
     * Lista de palavras disponíveis para geração de texto lorem ipsum
     * @var array
     */
    private $list = [0 => 'lorem', 1 => 'ipsum', 2 => 'dolor', 3 => 'sit', 4 => 'amet', 5 => 'consectetur', 6 => 'adipiscing', 7 => 'elit', 8 => 'praesent', 9 => 'interdum', 10 => 'dictum', 11 => 'mi', 12 => 'non', 13 => 'egestas', 14 => 'nulla', 15 => 'in', 16 => 'lacus', 17 => 'sed', 18 => 'sapien', 19 => 'placerat', 20 => 'malesuada', 21 => 'at', 22 => 'erat', 23 => 'etiam', 24 => 'id', 25 => 'velit', 26 => 'finibus', 27 => 'viverra', 28 => 'maecenas', 29 => 'mattis', 30 => 'volutpat', 31 => 'justo', 32 => 'vitae', 33 => 'vestibulum', 34 => 'metus', 35 => 'lobortis', 36 => 'mauris', 37 => 'luctus', 38 => 'leo', 39 => 'feugiat', 40 => 'nibh', 41 => 'tincidunt', 42 => 'a', 43 => 'integer', 44 => 'facilisis', 45 => 'lacinia', 46 => 'ligula', 47 => 'ac', 48 => 'suspendisse', 49 => 'eleifend', 50 => 'nunc', 51 => 'nec', 52 => 'pulvinar', 53 => 'quisque', 54 => 'ut', 55 => 'semper', 56 => 'auctor', 57 => 'tortor', 58 => 'mollis', 59 => 'est', 60 => 'tempor', 61 => 'scelerisque', 62 => 'venenatis', 63 => 'quis', 64 => 'ultrices', 65 => 'tellus', 66 => 'nisi', 67 => 'phasellus', 68 => 'aliquam', 69 => 'molestie', 70 => 'purus', 71 => 'convallis', 72 => 'cursus', 73 => 'ex', 74 => 'massa', 75 => 'fusce', 76 => 'felis', 77 => 'fringilla', 78 => 'faucibus', 79 => 'varius', 80 => 'ante', 81 => 'primis', 82 => 'orci', 83 => 'et', 84 => 'posuere', 85 => 'cubilia', 86 => 'curae', 87 => 'proin', 88 => 'ultricies', 89 => 'hendrerit', 90 => 'ornare', 91 => 'augue', 92 => 'pharetra', 93 => 'dapibus', 94 => 'nullam', 95 => 'sollicitudin', 96 => 'euismod', 97 => 'eget', 98 => 'pretium', 99 => 'vulputate', 100 => 'urna', 101 => 'arcu', 102 => 'porttitor', 103 => 'quam', 104 => 'condimentum', 105 => 'consequat', 106 => 'tempus', 107 => 'hac', 108 => 'habitasse', 109 => 'platea', 110 => 'dictumst', 111 => 'sagittis', 112 => 'gravida', 113 => 'eu', 114 => 'commodo', 115 => 'dui', 116 => 'lectus', 117 => 'vivamus', 118 => 'libero', 119 => 'vel', 120 => 'maximus', 121 => 'pellentesque', 122 => 'efficitur', 123 => 'class', 124 => 'aptent', 125 => 'taciti', 126 => 'sociosqu', 127 => 'ad', 128 => 'litora', 129 => 'torquent', 130 => 'per', 131 => 'conubia', 132 => 'nostra', 133 => 'inceptos', 134 => 'himenaeos', 135 => 'fermentum', 136 => 'turpis', 137 => 'donec', 138 => 'magna', 139 => 'porta', 140 => 'enim', 141 => 'curabitur', 142 => 'odio', 143 => 'rhoncus', 144 => 'blandit', 145 => 'potenti', 146 => 'sodales', 147 => 'accumsan', 148 => 'congue', 149 => 'neque', 150 => 'duis', 151 => 'bibendum', 152 => 'laoreet', 153 => 'elementum', 154 => 'suscipit', 155 => 'diam', 156 => 'vehicula', 157 => 'eros', 158 => 'nam', 159 => 'imperdiet', 160 => 'sem', 161 => 'ullamcorper', 162 => 'dignissim', 163 => 'risus', 164 => 'aliquet', 165 => 'habitant', 166 => 'morbi', 167 => 'tristique', 168 => 'senectus', 169 => 'netus', 170 => 'fames', 171 => 'nisl', 172 => 'iaculis', 173 => 'cras', 174 => 'aenean'];

    /**
     * Gera um array de parágrafos de texto lorem ipsum
     *
     * @param int $nParagraphs Quantidade de parágrafos a serem gerados
     * @return array Parágrafos gerados
     */
    public function generate($nParagraphs) {
        $paragraphs = [];

        // Para cada parágrafo solicitado
        for($p = 0; $p < $nParagraphs; ++$p) {
            // Define quantidade aleatória de sentenças por parágrafo
            $nSentences = random_int(2, 4);
            $sentences = [];

            // Para cada sentença
            for($s = 0; $s < $nSentences; ++$s) {
                $frags = [];
                $commaChance = .33;

                // Gera fragmentos de frases, podendo adicionar vírgulas
                while(true) {
                    $nWords = mt_rand(5, 15); // Número aleatório de palavras
                    $words = $this->random_values($this->list, $nWords);
                    $frags[] = implode(' ', $words);

                    // Probabilidade de adicionar mais fragmentos separados por vírgula
                    if($this->random_float() >= $commaChance) {
                        break;
                    }
                    $commaChance /= 2;
                }
                // Monta a sentença com inicial maiúscula e ponto final
                $sentences[] = ucfirst(implode(', ', $frags)) . '.';
            }
            // Junta as sentenças em um parágrafo
            $paragraphs[] = implode(' ', $sentences);
        }
        // Retorna o array de parágrafos
        return $paragraphs;
    }

    /**
     * Gera um número aleatório de ponto flutuante entre 0 e 1
     * @return float
     */
    private function random_float() {
        return random_int(0, PHP_INT_MAX-1) / PHP_INT_MAX;
    }

    /**
     * Retorna um array com valores aleatórios do array de palavras
     * @param array $arr Array de palavras
     * @param int $count Quantidade de palavras
     * @return array
     */
    private function random_values($arr, $count) {
        $keys = array_rand($arr, $count);
        if($count == 1) {
            $keys = [$keys];
        }
        return array_intersect_key($arr, array_fill_keys($keys, null));
    }
}