<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TicketRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     */
    public function index(TicketRepository $ticketRepository, Security $security): Response
    {
        return $this->render('profile/index.html.twig', [
            'tickets' => $ticketRepository->findAllByUser($security->getUser()),
        ]);
    }
}
