<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\Message;

use App\Form\TicketType;
use App\Form\MessageType;
use App\Form\ModeratorType;

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
     * @Route("/{id}", name="ticket_show", methods={"GET", "POST"})
     */
    public function show(Ticket $ticket, Request $request): Response
    {
        $moderators = [];
        foreach($ticket->getModerator() as $moderator)
        {
            $moderators[] = $moderator;
        }

        if(in_array('ROLE_ADMIN', $this->user->getRoles()) || $this->user == $ticket->getUser() || in_array($this->user, $moderators))
        {
            $message = new Message();

            $formMessage = $this->createForm(MessageType::class, $message);
            $formMessage->handleRequest($request);
            $message->setUser($this->user);
            $message->setTicket($ticket);

            $formModerator = $this->createForm(ModeratorType::class, $ticket);
            $formModerator->handleRequest($request);

            if($formMessage->isSubmitted() && $formMessage->isValid())
            {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($message);
                $entityManager->flush();

                return $this->redirect($request->getRequestUri());
            }

            if($formModerator->isSubmitted() && $formModerator->isValid())
            {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($ticket);
                $entityManager->flush();

                return $this->redirect($request->getRequestUri());
            }

            return $this->render('ticket/show.html.twig', [
                'ticket' => $ticket,
                'form_message' => $formMessage->createView(),
                'form_moderator' => $formModerator->createView()
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
        if(in_array('ROLE_ADMIN', $this->user->getRoles()))
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
        if(in_array('ROLE_ADMIN', $this->user->getRoles()))
        {
            if ($this->isCsrfTokenValid('delete'.$ticket->getId(), $request->request->get('_token'))) 
            {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($ticket);
                $entityManager->flush();
            }
    
            return $this->redirectToRoute('ticket');
        }
    }
}
