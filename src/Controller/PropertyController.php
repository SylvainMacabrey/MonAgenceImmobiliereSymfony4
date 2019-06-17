<?php
namespace App\Controller;

use App\Entity\Property;
use App\Entity\PropertySearch;
use App\Form\PropertySearchType;
use Symfony\Component\Form;
use App\Repository\PropertyRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Common\Persistence\ObjectManager;
use Twig\Environment;
use Knp\Component\Pager\PaginatorInterface;

class PropertyController extends AbstractController {

	/**
	* @var PropertyRepository
	*/
	private $repository;

	/**
	* @var ObjectManager
	*/
	private $em;

	public function __construct(PropertyRepository $repository, ObjectManager $em) {
        $this->repository = $repository;
        $this->em = $em;
    }

	/**
	* @Route("/biens", name="property.index")
	* @return Response
	*/
	public function index(PaginatorInterface $paginator, Request $request): Response {
		$search = new PropertySearch();
		$form = $this->createForm(PropertySearchType::class, $search);
		$form->handleRequest($request);
        $properties = $paginator->paginate(
        	$this->repository->findAllVisibleQuery($search), 
        	$request->query->getInt('page', 1),
        	12
    	);
        $this->em->flush();
		return $this->render('property/index.html.twig', [
			'courrent_menu' => 'properties_menu',
			'properties' => $properties,
			'form' => $form->createView(),
		]);
	}

	/**
	* @Route("/biens/{slug}-{id}", name="property.show", requirements={"slug": "[a-zA-Z\-]*"})
	* @return Response
	*/
	public function show(Property $property, String $slug): Response {
		if($property->getSlug() !== $slug) {
			return $this->redirectToRoute('property.show', [
				'id' => $property->getId(),
				'slug' => $property->getSlug()
			], 301);
		}
		return $this->render('property/show.html.twig', [
			'property' => $property
		]);
	}

}
	