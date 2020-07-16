<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Letter;
use App\Entity\Word;
use App\Repository\GameRepository;
use App\Repository\LetterRepository;
use App\Repository\WordRepository;
use App\Services\LetterManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/word", name="word_")
 */
class WordController extends AbstractController
{
    /**
     * @Route("/check", name="check")
     * @param GameRepository $gameRepository
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     */
    public function check(
        Request $request,
        GameRepository $gameRepository,
        EntityManagerInterface $entityManager,
        LetterManager $letterManager,
        LetterRepository $letterRepository
)
    {
        $game = $gameRepository->findOneBy([]);
        $letterTypedByUser = $request->query->get('letter');

        $letter = new Letter();
        $letter->setLetter($letterTypedByUser);

        $wordLetters = str_split($game->getWord()->getWord());
        $win  = false;
        if (in_array($letterTypedByUser, $wordLetters)) {
            $win = true;
        }
        if ($win === true) {
            $letter->setIsInTheWord($win);
            $entityManager->persist($letter);
            $entityManager->flush();

            $this->addFlash('success', 'Bien joué !');

            $wordAppareance = $letterManager->wordAppareance($game->getWord()->getWord(), $letterRepository->findAll());
            if ($letterManager->hasWonWord($wordAppareance, $game->getWord()->getWord())) {
                $this->addFlash('success', 'Bravo, tu as gagné !');
            }

            return $this->redirectToRoute('word_word');
        } else {
            $letter->setIsInTheWord($win);
            $entityManager->persist($letter);
            $game->setStep($game->getStep() +1);
            $entityManager->persist($game);
            $entityManager->flush();
            $this->addFlash('danger', 'Tu feras mieux la prochaine fois !');

        }
        return $this->redirectToRoute('word_word');
    }

    /**
     * @Route("/replay", name="replay")
     */
    public function replay(GameRepository $gameRepository,
                           EntityManagerInterface $entityManager,
                           WordRepository $wordRepository,
                           LetterRepository $letterRepository)
    {
        $game = $gameRepository->findOneBy([]);
        $words = $wordRepository->findAll();
        shuffle($words);
        $word = $words[0];
        $game->setStep(0)->setWord($word);

        $letters = $letterRepository->findAll();
        foreach ($letters as $letter) {
            $entityManager->remove($letter);
        }

        $entityManager->persist($game);
        $entityManager->flush();
        return $this->redirectToRoute('word_word');
    }

    /**
     * @Route("/word", name="word")
     */
    public function index(GameRepository $gameRepository, LetterRepository $letterRepository, LetterManager $letterManager)
    {
        $letters = $letterRepository->findBy(['isInTheWord' => false]);
        $game = $gameRepository->findOneBy([]);

        $lettersTrue = $letterRepository->findBy(['isInTheWord' => true]);

        $word = $game->getWord()->getWord();
        $wordLettersResult = $letterManager->wordAppareance($word, $lettersTrue);


        return $this->render('word/index.html.twig', [
            'game' => $game,
            'letters' => $letters,
            'wordLettersResult' => $wordLettersResult,
        ]);
    }
}
