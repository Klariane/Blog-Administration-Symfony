<?php

namespace App\Controller;

use App\Form\UserType;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use App\Entity\User;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Form\CategoryType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', []);
    }

    /**
     * @Route("/admin/articles", name="admin_articles")
     */

    public function adminArticles(ArticleRepository $repo, EntityManagerInterface $manager)
    {
        $colonnes = $manager->getClassMetadata(Article::class)->getFieldNames(); //nous selectionnons le nom des champs/colonnes de la table Article dans la BDD

        $articles = $repo->findAll(); //récuperer tous les articles dans la BDD

        return $this->render('admin/admin_articles.html.twig',[ //je vais renvoyer le tout à la vue 'admin/admin_articles.html.twig'
            'articles'=>$articles,  //cette vue va renvoyer tous les articles
            'colonnes'=>$colonnes // et le nom des champs
        ]);

    }

    /**
     * @Route("/admin/users", name="admin_users")
     */
    public function adminUsers(UserRepository $userRepo, EntityManagerInterface $manager)
    {
        $colonnesUsers = $manager->getClassMetadata(User::class)->getFieldNames();

        $users= $userRepo->findAll();
        return $this->render('admin/admin_users.html.twig', [
            'users'=>$users,
            'colonnesUsers'=>$colonnesUsers
        ]);
    }

    /**
     * @Route("admin/user/new", name="admin_new_user")
     * @Route("admin/{id}/edit-user", name="admin_edit_user")
     */
    public function formUser(EntityManagerInterface $manager, Request $request, User $user=null,UserPasswordEncoderInterface $passwordEncoder)
    {
        if(!$user)
        {
            $user = new User;
        }

        $formUser = $this->createForm(UserType::class, $user);
        $formUser->handleRequest($request);

        if($formUser->isSubmitted() && $formUser->isValid())
        {  
            if(!$user->getId())
            {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $formUser->get('password')->getData()
                    )
                );
            }
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success', 'Les modifications ont bien été enrégistrées !');
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/form_user.html.twig', [
            'formUserEdit'=> $formUser->createView(),
            'editMode'=> $user->getId() !== NULL 
        ]);

    }

       /**
     * @Route("/admin/{id}/delete-user", name="admin_delete_user")
     */

    public function deleteUser(EntityManagerInterface $manager, User $user)
    {
        //Symfony fait automatiquement le lien entre l'objet $article et l'id indiqué dans l'url
       $manager->remove($user);
       $manager->flush();

       $this->addflash('success',"l'utilisateur' '". $user->getPrenom()." ". $user->getNom(). "' a bien été supprimé");
       return $this->redirectToRoute("admin_users");

    }








    /**
     * @Route("/admin/commentaires", name="admin_commentaires")
     */
    public function adminCommentaires(CommentRepository $commentRepo, EntityManagerInterface $manager)
    {
        $colonnesCommentaires = $manager->getClassMetadata(Comment::class)->getFieldNames();

        $commentaires = $commentRepo->findAll();

        return $this->render('admin/admin_commentaires.html.twig',[ 
            'commentaires'=>$commentaires,  
            'colonnesCommentaires'=>$colonnesCommentaires
        ]);
    }

    /**
     * @Route("/admin/commentaire/new", name="admin_new_commentaire")
     * @Route("/admin/{id}/edit-commentaire", name="admin_edit_commentaire")
     */
    public function formCommentaire(EntityManagerInterface $manager, Request $request, Comment $commentaire=null): Response 
    {
        if (!$commentaire)
        {
            $commentaire = new Comment;
        }

        $formCommentaire = $this->createForm(CommentType::class, $commentaire);
        $formCommentaire->handleRequest($request); //permet de gérer le traitement de la saisie du formulaire.

        if($formCommentaire->isSubmitted() && $formCommentaire->isValid())
        {
            if(!$commentaire->getId())
            {
                $commentaire->setCreatedAt(new \DateTime());
            }

            $manager->persist($commentaire);
            $manager->flush();
            $this->addFlash('success', 'Les modifications ont bien été enrégistrées !');
            return $this->redirectToRoute('admin_commentaires');
        }
        return $this->render('admin/form_commentaires.html.twig', [
            'formCommentaireEdit'=> $formCommentaire->createView(),
            'editMode'=> $commentaire->getId() !== NULL 
        ]);
    }


    /**
     * @Route("/admin/{id}/delete-commentaire", name="admin_delete_commentaire")
     */

     public function deleteCommentaire(EntityManagerInterface $manager, Comment $commentaire)
     {
        $manager->remove($commentaire);
        $manager->flush();

        $this->addflash('success',"le commentaire de '". $commentaire->getAuthor(). "' a bien été supprimé");
        return $this->redirectToRoute("admin_commentaires");

     }




    /**
     * @Route("/admin/categories", name="admin_categories")
     */
    public function adminCategories(CategoryRepository $cateRepo, EntityManagerInterface $manager)
    {
        $colonnesCategories = $manager->getClassMetadata(Category::class)->getFieldNames();

        $categories = $cateRepo->findAll();

        return $this->render('admin/admin_categories.html.twig', [
            'colonnesCategories' => $colonnesCategories,
            'categories'    => $categories
        ]);

    }

    /**
     * @Route("admin/categorie/new", name="admin_new_categorie")
     * @Route("admin/{id}/edit-categorie", name="admin_edit_categorie")
     */
    public function formCategorie(EntityManagerInterface $manager, Request $request, Category $categorie=null)
    {
        if(!$categorie)
        {
            $categorie = new Category;
        }

        $formCategorie = $this->createForm(CategoryType::class, $categorie);
        $formCategorie->handleRequest($request);

        if($formCategorie->isSubmitted() && $formCategorie->isValid())
        {
            $manager->persist($categorie);
            $manager->flush();
            $this->addFlash('success', 'Les modifications ont bien été enrégistrées !');
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/form_categorie.html.twig', [
            'formCategorieEdit'=> $formCategorie->createView(),
            'editMode'=> $categorie->getId() !== NULL 
        ]);

    }

       /**
     * @Route("/admin/{id}/delete-categorie", name="admin_delete_categorie")
     */

    public function deleteCategorie(EntityManagerInterface $manager, Category $categorie)
    {
        //Symfony fait automatiquement le lien entre l'objet $article et l'id indiqué dans l'url
       $manager->remove($categorie);
       $manager->flush();

       $this->addflash('success',"la catégorie '". $categorie->getTitle(). "' a bien été supprimé");
       return $this->redirectToRoute("admin_categories");

    }



    /**
     * @Route("/admin/article/new", name="admin_new_article")
     * @Route("/admin/{id}/edit-article", name="admin_edit_article")
     */
    public function form(EntityManagerInterface $manager, Request $request, Article $article=null): Response //un controller renvoit tjrs une reponse
    {
        if (!$article)
        {
            $article = new Article;
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request); //permet de gérer le traitement de la saisie du formulaire.

        if($form->isSubmitted() && $form->isValid())
        {
            if(!$article->getId())
            {
                $article->setCreatedAt(new \DateTime());
            }

            $manager->persist($article);
            $manager->flush();
            $this->addFlash('success', 'Les modifications ont bien été enrégistrées !');
            return $this->redirectToRoute('admin_articles');
        }
        return $this->render('admin/form_article.html.twig', [
            'formEdit'=> $form->createView(),
            'editMode'=> $article->getId() !== NULL //si l'id de larticle n'est pas null alors on est entrain d'éditer, donc editMode =1; sinon editMode = 0. dans ce cas on est dans la création d'article.
        ]);
    }


    /**
     * @Route("/admin/{id}/delete-article", name="admin_delete_article")
     */

     public function deleteArticle(EntityManagerInterface $manager, Article $article)
     {
         //Symfony fait automatiquement le lien entre l'objet $article et l'id indiqué dans l'url
        $manager->remove($article);
        $manager->flush();

        $this->addflash('success',"l'article '". $article->getTitle(). "' a bien été supprimé");
        return $this->redirectToRoute("admin_articles");

     }


}
