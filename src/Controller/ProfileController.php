<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     */
    public function index(Security $security): Response
    {
        return $this->render('profile/index.html.twig', [
            'tickets' => $security->getUser()->getTickets(),
            'admin_tickets' => $security->getUser()->getAdminTickets(),
        ]);
    }
}
