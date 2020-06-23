<?php

namespace App\Controller;

use App\Entity\Game;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function check(Request $request, $gameRepository, EntityManagerInterface $entityManager)
    {
        $game = $gameRepository->findOneBy([]);
        $letter = $request->query->get('letter');
        $wordLetters = str_split($game->getWord()->getWord());
        $win  = false;
        if (in_array($letter, $wordLetters)) {
            $win = true;
            $this->addFlash('success', 'Bien joué !');
        }
        if ($win === true) {
            return $this->redirectToRoute('word_word');
        } else {
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
    public function replay(GameRepository $gameRepository, EntityManagerInterface $entityManager)
    {
        $game = $gameRepository->findOneBy([]);
        $game->setStep(0);
        $entityManager->persist($game);
        $entityManager->flush();
        return $this->redirectToRoute('word_word');
    }

    /**
     * @Route("/word", name="word")
     */
    public function index(GameRepository $gameRepository)
    {
        $game = $gameRepository->findOneBy([]);
        return $this->render('word/index.html.twig', [
            'game' => $game,
        ]);
    }
}
