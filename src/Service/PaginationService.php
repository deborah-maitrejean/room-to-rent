<?php

namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class PaginationService
{
    private $entityClass; // il doit connaître l'entité sur laquelle il va faire la pagination
    private $limit = 10; // à 10 par défaut
    private $currentPage = 1; // à 1 par défaut
    private $manager;
    private $twig;
    private $templatePath;

    // services et dépendances : dans un service, l'injection se fait via le constructeur
    public function __construct(ObjectManager $manager, Environment $twig, RequestStack $requestStack, $templatePath)
    {

        $this->manager = $manager; // est égal au manager que me passe Symfony
        $this->twig = $twig;
        $this->route = $requestStack->getCurrentRequest()->attributes->get('_route');
        $this->templatePath = $templatePath;
    }

    // on veut lui faire afficher un template particulier
    public function display()
    {
        $this->twig->display($this->templatePath, [
            'page' => $this->currentPage,
            'pages' => $this->getNbPages(),
            'route' => $this->route,
        ]);
    }

    /**
     * Permet de récupérer le nombre de pages qui existent sur une entité particulière
     *
     * Elle se sert de Doctrine pour récupérer le repository qui correspond à l'entité que l'on souhaite
     * paginer (voir la propriété $entityClass) puis elle trouve le nombre total d'enregistrements grâce
     * à la fonction findAll() du repository
     *
     * @return int
     * @throws \Exception si la propriété $entityClass n'est pas configurée
     */
    public function getNbPages()
    {
        if(empty($this->entityClass)) {
            // Si il n'y a pas d'entité configurée, on ne peut pas charger le repository, la fonction
            // ne peut donc pas continuer !
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer ! Utilisez la méthode setEntityClass() de votre objet PaginationService !");
        }

        // 1) Connaître le total des enregitrements de la table
        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());

        // 2) Faire la division, l'arrondi, et le renvoyer
        $nbPages = ceil($total / $this->limit);
        return $nbPages;
    }

    /**
     * Permet de récupérer les données paginées pour une entité spécifique
     *
     * Elle se sert de Doctrine afin de récupérer le repository pour l'entité spécifiée
     * puis grâce au repository et à sa fonction findBy() on récupère les données dans une
     * certaine limite et en partant d'un offset
     *
     * @throws \Exception si la propriété $entityClass n'est pas définie
     *
     * @return array
     */
    public function getData()
    {
        if(empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer ! Utilisez la méthode setEntityClass() de votre objet PaginationService !");
        }

        // 1) Calculer l'offset, cad quand on démarre
        $offset = $this->currentPage * $this->limit - $this->limit; // page sur laquelle je me trouve, multiiplié par la limite, moins la limite

        // 2) Demander au repository de trouver les éléments
        $repository = $this->manager->getRepository($this->entityClass);
        $data = $repository->findby([], [], $this->limit, $offset);

        // 3) Envoyer les éléments en question
        return $data;
    }

    /**
     * @param mixed $entityClass
     * @return PaginationService
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * @param mixed $limit
     * @return PaginationService
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param mixed $currentPage
     * @return PaginationService
     */
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @param ObjectManager $manager
     * @return PaginationService
     */
    public function setManager(ObjectManager $manager)
    {
        $this->manager = $manager;
        return $this;
    }

    /**
     * @return ObjectManager
     */
    public function getManager(): ObjectManager
    {
        return $this->manager;
    }

    /**
     * @param mixed $templatePath
     * @return PaginationService
     */
    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }
}