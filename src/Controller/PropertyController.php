<?php
namespace App\Controller;

use App\Entity\Property;
use App\Entity\PropertySearch;
use App\Entity\Contact;
use App\Form\PropertySearchType;
use App\Form\ContactType;
use App\Repository\PropertyRepository;
use App\Notification\ContactNotification;
use Symfony\Component\Form;
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
	public function show(Property $property, String $slug, Request $request, ContactNotification $notification): Response {
		if($property->getSlug() !== $slug) {
			return $this->redirectToRoute('property.show', [
				'id' => $property->getId(),
				'slug' => $property->getSlug()
			], 301);
		}
		$contact = new Contact();
		$contact->setProperty($property);
		$form = $this->createForm(ContactType::class, $contact);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$notification->notify($contact);
			$this->addFlash('success', 'Votre message a été envoyé');
			return $this->redirectToRoute('property.show', [
				'id' => $property->getId(),
				'slug' => $property->getSlug()
			]);
		}
		return $this->render('property/show.html.twig', [
			'property' => $property,
			'form' => $form->createView()
		]);
	}

}
	