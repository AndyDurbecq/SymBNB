<?php

namespace App\Service;

use Twig\Environment;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;

class PaginationService {
    private $entityClass;
    private $limit = 10;
    private $currentPage = 1;
    private $manager;
    private $twig;
    private $route;
    private $templatePath;
    
    public function __construct(ObjectManager $manager, Environment $twig, RequestStack $request, $templatePath)
    {
        $this->manager = $manager;
        $this->twig = $twig;
        $this->templatePath = $templatePath;
        $this->route = $request->getCurrentRequest()->attributes->get('_route');
    }

    public function display()
    {
        $this->twig->display($this->templatePath, [
            'page' => $this->currentPage,
            'pages' => $this->getPages(),
            'route' => $this->route
        ]);
    }

    public function getDatas()
    {
        if (empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié l'entité ! Utilisez la méthode setEntityClass de votre objet Pagination !");
        }

        $offset = $this->currentPage * $this->limit - $this->limit;

        $repo = $this->manager->getRepository($this->entityClass);

        $datas = $repo->findBy([], [], $this->limit, $offset);

        return $datas;
    }

    public function getPages()
    {
        if (empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié l'entité ! Utilisez la méthode setEntityClass de votre objet Pagination !");
        }

        $repo = $this->manager->getRepository($this->entityClass);

        $total = count($repo->findAll());

        $pages = ceil($total / $this->limit);

        return $pages;
    }

    public function setEntityClass($entityClass) : self
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    public function getEntityClass() 
    {
        return $this->entityClass;
    }

    public function setLimit($limit) : self
    {
        $this->limit = $limit;

        return $this;
    }

    public function getLimit() 
    {
        return $this->limit;
    }

    public function setPage($page) : self
    {
        $this->currentPage = $page;

        return $this;
    }

    public function getPage() 
    {
        return $this->currentPage;
    } 
    
    public function setRoute($route) : self
    {
        $this->route = $route;

        return $this;
    }

    public function getRoute() 
    {
        return $this->route;
    } 

    public function setTemplatePath($templatePath) : self
    {
        $this->templatePath = $templatePath;

        return $this;
    }

    public function getTemplatePath() 
    {
        return $this->templatePath;
    } 
}