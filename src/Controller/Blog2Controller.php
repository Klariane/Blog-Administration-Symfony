<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Contact;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Form\ContactType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Notification\ContactNotification;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Blog2Controller extends AbstractController
{
    /**
     * @Route("/blog2", name="blog2")
     */
    public function index(ArticleRepository $repo, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createFormBuilder(NULL) //Construction dun formulaire de recherche
        ->add('Recherche')
        ->getForm();//création du formulaire


        $form->handleRequest($request); //recollter les infos qui viennent du formulaire
        if($form->isSubmitted() && $form->isValid())
        {
           $resultat = $form->get('Recherche')->getData(); //la variable $resultat contient ce que l'utilisateur recherche
           
            $articles= $repo->getArticleByName($resultat);//jappelle la methode que jai crée dans ArticleRepository.php et je lui passe en argument la chaine de caractère que l'utilisateur recherche
        //    $articles = $repo->findBy(
        //        array('title'=>$resultat) //On recupère les articles dont le nom "title" est égal à ce  qu'on recherche($resultat)
        //    );
        }
        else{
        $articles = $repo->getArticlesOrderByDate();//SELECT * FROM Articles +fetchAll

    }
    //si on utilise le formulaire je veux recupérer les articles recherchés 
    //sinon, je veux récupérer tous les articles en BDD



        return $this->render('blog2/index.html.twig', [
            'articles' => $articles,
            'searchForm'=> $form->createView() //envoie du formulaire à la vie
        ]);
    }

    /**
     * @Route("/blog2/new", name="blog2_create")
     * @Route("/blog2/edit/{id}", name="blog2_edit")
     */

    public function form(EntityManagerInterface $manager, Request $request, Article $article=null)
    {
        if(!$article){
            $article = new Article;
        }

        $form = $this->createForm(ArticleType::class, $article);
        //on fait appel à la classe ArticleType permettant de generer le formulaire dajout/de modification
        //On précise que ce formulaire permettra de remplir un objet issu de la classe Article
        $form ->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            if(!$article->getId())
            {$article->setCreatedAt(new \Datetime());}

            $manager->persist($article);
            $manager->flush();
            return $this->redirectToRoute('blog2_show', [
                'id' => $article->getId()
            ]);

            //apres creation/edition dun article.je suis redirigé vers la page de cet article

        }
        return $this->render('blog2/create.html.twig',[
            'formArticle'=> $form->createView(), //jenvoie le formulaire à la vue
            'editMode'=> $article->getId() !== NULL //si l'id de larticle nest pas NULL, larticle existe => nous sommes en train dediter.
        ]);
    }


    /**
     * @route("/blog2/delete/{id}", name="blog2_delete")
     */
    public function delete(Article $article,EntityManagerInterface $manager) : Response
    {
        $manager->remove($article);
        $manager->flush();
        return $this->redirectToRoute("blog2");
    }

    /**
     * @route("/blog2/contact", name="blog2_contact")
     */

    public function contact(Request $request, EntityManagerInterface $manager, ContactNotification $notification):Response
    {
        $contact =new Contact;
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())

        {
            $manager->persist($contact);
            $manager->flush();
            $notification->notify($contact);
            $this->addFlash('success', 'Votre message a bien été envoyé !');
            return new RedirectResponse('/blog2/contact');
        }
        return $this->render("blog2/contact.html.twig", [
            'formContact'=>$form->createView() // ce qui se trouve entre les crochets est ce que la methode du controller envoie a la vue(ici, la methode contact(envoie le formulaire à la vue contact.html.twig))
        ]);
    }


    /**
     * @Route("/blog2/{id}", name="blog2_show")
     */

     public function show($id, ArticleRepository $repo, Request $rq, EntityManagerInterface $manager) : Response
     {  
         $comment = new Comment;
         $form=$this->createForm(CommentType::class, $comment);
         $form->handleRequest($rq);

         $article = $repo->find($id);

         $user= $this->getUser(); // on récupère les infos de lutilisateur connecté

        if($form->isSubmitted() && $form->isValid())
        {   $comment->setCreatedAt(new \DateTime());
            $comment->setAuthor($user->getUsername());
            $comment->setArticle($article);

            $manager->persist($comment);
            $manager->flush();
            $this->addFlash('success','Commentaire envoyé !');
            $this->redirectToRoute('blog2_show',[
                'id' => $id
            ]);
        }

         
        return $this->render('blog2/show.html.twig',[
             'article' => $article,
             'commentForm'=> $form->createView()
         ]);
     }
}
