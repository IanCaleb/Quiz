<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $questions = [
            [
                'question' => 'Quanto é 7 + 5?',
                'option_a' => '10',
                'option_b' => '11',
                'option_c' => '12',
                'option_d' => '13',
                'correct_answer' => 'c',
                'category' => 'matematica_basica',
            ],
            [
                'question' => 'Quanto é 9 - 4?',
                'option_a' => '3',
                'option_b' => '4',
                'option_c' => '5',
                'option_d' => '6',
                'correct_answer' => 'c',
                'category' => 'matematica_basica',
            ],
            [
                'question' => 'Quanto é 6 × 8?',
                'option_a' => '42',
                'option_b' => '46',
                'option_c' => '48',
                'option_d' => '54',
                'correct_answer' => 'c',
                'category' => 'matematica_basica',
            ],
            [
                'question' => 'Quanto é 81 ÷ 9?',
                'option_a' => '7',
                'option_b' => '8',
                'option_c' => '9',
                'option_d' => '10',
                'correct_answer' => 'c',
                'category' => 'matematica_basica',
            ],
            [
                'question' => 'Qual é a metade de 50?',
                'option_a' => '20',
                'option_b' => '25',
                'option_c' => '30',
                'option_d' => '35',
                'correct_answer' => 'b',
                'category' => 'matematica_basica',
            ],
            [
                'question' => 'Quanto é 2^5 (2 elevado a 5)?',
                'option_a' => '16',
                'option_b' => '24',
                'option_c' => '32',
                'option_d' => '64',
                'correct_answer' => 'c',
                'category' => 'matematica_basica',
            ],
            [
                'question' => 'Qual o valor de π (aproximado) até duas casas decimais?',
                'option_a' => '3,00',
                'option_b' => '3,14',
                'option_c' => '3,28',
                'option_d' => '3,50',
                'correct_answer' => 'b',
                'category' => 'matematica_basica',
            ],
            [
                'question' => 'Quanto é 15% de 200?',
                'option_a' => '20',
                'option_b' => '25',
                'option_c' => '30',
                'option_d' => '35',
                'correct_answer' => 'c',
                'category' => 'matematica_basica',
            ],
            [
                'question' => 'Qual é o mínimo múltiplo comum (MMC) de 4 e 6?',
                'option_a' => '10',
                'option_b' => '12',
                'option_c' => '14',
                'option_d' => '16',
                'correct_answer' => 'b',
                'category' => 'matematica_basica',
            ],
            [
                'question' => 'Quanto é 100 - 37?',
                'option_a' => '63',
                'option_b' => '64',
                'option_c' => '65',
                'option_d' => '66',
                'correct_answer' => 'a',
                'category' => 'matematica_basica',
            ],
            [
                'question' => 'Qual é a soma de 1 + 2 + 3 + 4 + 5?',
                'option_a' => '10',
                'option_b' => '12',
                'option_c' => '15',
                'option_d' => '20',
                'correct_answer' => 'c',
                'category' => 'matematica_basica',
            ],
            [
                'question' => 'Se um número x satisfaz x + 3 = 10, qual é x?',
                'option_a' => '6',
                'option_b' => '7',
                'option_c' => '8',
                'option_d' => '9',
                'correct_answer' => 'b',
                'category' => 'matematica_basica',
            ],
            [
                'question' => 'Quanto é a média aritmética de 4, 6 e 10?',
                'option_a' => '6',
                'option_b' => '7',
                'option_c' => '8',
                'option_d' => '9',
                'correct_answer' => 'b',
                'category' => 'matematica_basica',
            ],
            [
                'question' => 'Qual é o valor de 0,25 em forma de fração?',
                'option_a' => '1/2',
                'option_b' => '1/3',
                'option_c' => '1/4',
                'option_d' => '1/5',
                'correct_answer' => 'c',
                'category' => 'matematica_basica',
            ],
            [
                'question' => 'Quanto é 11 × 11?',
                'option_a' => '111',
                'option_b' => '121',
                'option_c' => '131',
                'option_d' => '141',
                'correct_answer' => 'b',
                'category' => 'matematica_basica',
            ],
            [
                'question' => 'Qual número completa a sequência: 2, 4, 6, __?',
                'option_a' => '7',
                'option_b' => '8',
                'option_c' => '9',
                'option_d' => '10',
                'correct_answer' => 'b',
                'category' => 'matematica_basica',
            ],
            [
                'question' => 'Quanto é 5! (5 fatorial)?',
                'option_a' => '60',
                'option_b' => '90',
                'option_c' => '100',
                'option_d' => '120',
                'correct_answer' => 'd',
                'category' => 'matematica_basica',
            ],
            [
                'question' => 'Qual é a área de um quadrado de lado 7?',
                'option_a' => '14',
                'option_b' => '28',
                'option_c' => '49',
                'option_d' => '56',
                'correct_answer' => 'c',
                'category' => 'matematica_basica',
            ],
            [
                'question' => 'Se um trem parte às 14:00 e viaja 3 horas, que horas são quando chega?',
                'option_a' => '16:00',
                'option_b' => '17:00',
                'option_c' => '18:00',
                'option_d' => '19:00',
                'correct_answer' => 'b',
                'category' => 'matematica_basica',
            ],
            [
                'question' => 'Quanto é 14 + 28?',
                'option_a' => '32',
                'option_b' => '36',
                'option_c' => '40',
                'option_d' => '42',
                'correct_answer' => 'd',
                'category' => 'matematica_basica',
            ],
        ];

        foreach ($questions as $question) {
            Question::create($question);
        }
    }
}
