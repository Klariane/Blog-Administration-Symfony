<?php 

Namespace App\Notification;

use App\Entity\Contact;
use Twig\Environment;

class ContactNotification
{
    /**
     * @var \Swift_Mailer
     */

     private $mailer;

     /**
      * @var Environment
      */
      private $renderer;


      public function __construct(Environment $renderer, \Swift_Mailer $mailer)
      {
          $this->mailer = $mailer;
          $this->renderer = $renderer;
      }

      public function notify(Contact $contact)
      {
        $message = (new \Swift_Message('Nouveau message de : ' . $contact->getEmail()))
            ->setFrom($contact->getEmail()) //expediteur de lemail
            ->setTo('leboukan2005@yahoo.fr') //destinataire de l'email
            ->setReplyTo($contact->getEmail()) //adresse de réponse
            ->setBody($this->renderer->render('emails/contact.html.twig', [
                'contact' => $contact
                
            ]), 'text/html');

            $this->mailer->send($message);

                    
      }
}


?>