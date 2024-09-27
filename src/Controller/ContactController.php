<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $contactData = new ContactDTO();
        $contactData->name = '';
        $contactData->email = '';
        $contactData->message = '';
        $contactForm = $this->createForm(ContactType::class, $contactData);
        $contactForm->handleRequest($request);

        if ($contactForm->isSubmitted() && $contactForm->isValid()) {
            try{

                $mail = (new TemplatedEmail())
                    ->to('donnikurosaki@gmail.com')
                    ->from($contactData->email)
                    ->subject('Demande de contact envoyer depuis le blog 2CS')
                    ->htmlTemplate('emails/contact.html.twig')
                    ->context(['contactData' => $contactData]);
                
                $mailer->send($mail);
                $this->addFlash('success','Votre mail a bien été envoyé ');
                // $this->addFlash('success', '');
                $this->redirectToRoute('app_contact');
                } catch (\Exception $error) {
                    $this->addFlash('danger', "Votre email ne peut pas être envoyer");
                }
            }
        return $this->render('contact/index.html.twig', [
            'contactForm' => $contactForm,
        ]);
    }
}

