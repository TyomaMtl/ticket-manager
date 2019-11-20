<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Form\TicketType;
use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/ticket")
 */
class TicketController extends AbstractController
{
    private $user;

    public function __construct(Security $security) 
    {
        $this->user = $security->getUser();
    }

    /**
     * @Route("/", name="ticket", methods={"GET"})
     */
    public function index(TicketRepository $ticketRepository): Response
    {
        if(in_array('ROLE_ADMIN', $this->user->getRoles()))
        {
            return $this->render('ticket/index.html.twig', [
                'tickets' => $ticketRepository->findAll(),
            ]);
        }
        else
        {
            return $this->render('ticket/index.html.twig', [
                'error' => 'Vous ne pouvez pas accéder à cette ressource'
            ]);
        }
    }

    /**
     * @Route("/new", name="ticket_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);
        $ticket->setUser($this->user);
        $ticket->setCreatedAt();

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ticket);
            $entityManager->flush();

            return $this->redirectToRoute('profile');
        }

        return $this->render('ticket/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ticket_show", methods={"GET"})
     */
    public function show(Ticket $ticket): Response
    {
        if(in_array('ROLE_ADMIN', $this->user->getRoles()) || $this->user == $ticket->getUser())
        {
            return $this->render('ticket/show.html.twig', [
                'ticket' => $ticket,
            ]);
        }
        else
        {
            return $this->render('ticket/index.html.twig', [
                'error' => 'Vous ne pouvez pas accéder à cette ressource'
            ]);
        }
    }

    /**
     * @Route("/{id}/edit", name="ticket_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Ticket $ticket): Response
    {
        if($this->user == $ticket->getUser())
        {
            $form = $this->createForm(TicketType::class, $ticket);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) 
            {
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('ticket');
            }

            return $this->render('ticket/edit.html.twig', [
                'ticket' => $ticket,
                'form' => $form->createView(),
            ]);
        }
        else
        {
            return $this->render('ticket/index.html.twig', [
                'error' => 'Vous ne pouvez pas accéder à cette ressource'
            ]);
        }
    }

    /**
     * @Route("/{id}", name="ticket_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Ticket $ticket): Response
    {
        if(in_array('ROLE_ADMIN', $this->user->getRoles()) || $this->user == $ticket->getUser())
        {
            if ($this->isCsrfTokenValid('delete'.$ticket->getId(), $request->request->get('_token'))) 
            {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($ticket);
                $entityManager->flush();
            }
    
            return $this->redirectToRoute('ticket');
        }
        else
        {
            return $this->redirectToRoute('ticket');
        }
    }
}
