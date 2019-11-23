<?php

namespace App\Controller;

use App\Entity\Message;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("message")
 */
class MessageController extends AbstractController 
{
    /**
     * @Route("/{id}", name="message_delete", methods={"DELETE"})
     */
    public function delete(Security $security, Request $request, Message $message): RedirectResponse
    {
        if(in_array('ROLE_ADMIN', $security->getUser()->getRoles()))
        {
            if ($this->isCsrfTokenValid('delete'.$message->getId(), $request->request->get('_token'))) 
            {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($message);
                $entityManager->flush();
            }
    
            $referer = $request->headers->get('referer');
            return new RedirectResponse($referer);
        }
    }
}