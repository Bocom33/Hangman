<?php


namespace App\Services;

class LetterManager
{

    public function wordAppareance($word, $letters)
    {
        $wordLetters = str_split($word);

        $rightLetters = [];
        foreach ($letters as $trueLetter) {
            $rightLetters[] = $trueLetter->getLetter();
        }
        $result = [];
        foreach ($wordLetters as $letter){
            if (in_array($letter, $rightLetters)) {
                $result[] = $letter;
            }
            else {
                $result[] = '_';
            }
        }
        return $result;
    }

    public function hasWonWord($letters, $word){
        dump(implode($letters), $word);
        if ($word === implode($letters)) {
            $hasWon = true;
        } else {
            $hasWon = false;
        }
        return $hasWon;
    }
}
