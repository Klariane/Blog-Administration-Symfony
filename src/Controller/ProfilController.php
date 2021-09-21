<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfilEditType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfilController extends AbstractController
{
    /**
     * @Route("/profil", name="profil")
     */
    public function index(Request $request, EntityManagerInterface $manager): Response
    {
        $user=$this->getUser();//permet de recuperer les infos de lutilisateur connecté

        $form = $this->createForm(ProfilEditType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success', 'Votre profil a bien été modifié ! ');
            return $this->redirectToRoute('profil');
        }

        return $this->render('profil/index.html.twig', [
            'userForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/profil/password", name="password_edit")
     */
    public function edit(Request $rq, EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getUser();

        $form = $this->createFormBuilder($user)
        
        ->add('password', RepeatedType::class, [ //creer un champ + 1 champ de confirmation
            // instead of being set onto the object directly,
            // this is read and encoded in the controller
            'type'=>PasswordType::class,
            'first_options'=>['label'=>'Nouveau Mot de Passe'],
            'second_options'=>['label'=>'Confirmez votre nouveau mot de passe'],
            'mapped' => false,
            'attr' => ['autocomplete' => 'new-password'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer le mot de passe.',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Votre mot de passe doit être composé d\au moins 6 caractères.',
                    // max length allowed by Symfony for security reasons
                    'max' => 4096,
                ]),
            ],
        ])

            ->getForm();
            $form->handleRequest($rq);

            if($form->isSubmitted() && $form->isValid())
            {
                
                //ici dessus je recipere le mot de passe apres hashage
                
                    //si lancien mot de passe est identique au mot de passe que j'ai entré dans ""checkPassword"
               
                //hasher le mot de passe
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
            
                //$pwd = $form->get('password')->getData(); // on recupere le mot de passe brut entrée par lutilisateur
                //$user ->setPassword($pwd);// test de modification de mot de pass sans hachage
                $manager->persist($user);
                $manager->flush();
                $this->addFlash('success', 'Votre mot de passe a bien été modifié');
                return $this->redirectToRoute('profil');
            }

            return $this->render('profil/password_edit.html.twig', [
                'passwordForm' => $form->createView()
            ]);
    }
}
