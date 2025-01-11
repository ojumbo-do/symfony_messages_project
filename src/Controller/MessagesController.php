<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\MessageRepository;
use App\Form\MessageType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Message;


class MessagesController extends AbstractController
{

    #[Route("/message", name: "message_index")]
    public function index(MessageRepository $repository, Request $request, EntityManagerInterface $manager): Response


    {

        $message = new Message;

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($message);
            $manager->flush();
            $this->addFlash("notice", "Message added successfully!");
            return $this->redirectToRoute('message_index');

        }


        return $this->render("messages/index.html.twig", ["messages"=>$repository->findAll(), "form"=>$form]);
    }


    #[Route("/message/details/{id}", name: "message_details")]
    public function details(Message $message): Response

    {
       return $this->render('messages/details.html.twig', ["message"=>$message]);
    }

    #[Route("/message/{id}/delete", name: "delete_message")]
    public function delete(Message $message, Request $request, EntityManagerInterface $manager): Response

    {

        if($request->isMethod("POST")){
            $manager->remove($message);
            $manager->flush();
            $this->addFlash("notice", "Message deleted successfully!");
            return $this->redirectToRoute('message_index');
        }


       return $this->render('messages/delete.html.twig', ["id"=>$message->getId(), "message"=>$message]);
    }
}