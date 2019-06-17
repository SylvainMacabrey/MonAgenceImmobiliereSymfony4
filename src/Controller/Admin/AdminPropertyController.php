<?php
namespace App\Controller\Admin;

use App\Entity\Property;
use App\Repository\PropertyRepository;
use App\Form\PropertyType;
use Symfony\Component\Form;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Common\Persistence\ObjectManager;
use Twig\Environment;

class AdminPropertyController extends AbstractController {

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
	* @Route("/admin", name="admin.property.index")
	* @return Response
	*/
	public function index(): Response {
		$properties = $this->repository->findAll();
		return $this->render('admin/property/index.html.twig', [
			'properties' => $properties,
			'courrent_menu' => 'administration',
		]);
	}

	/**
	* @Route("/admin/property/create", name="admin.property.new")
	* @return Response
	*/
	public function new(Request $request): Response {
		$property = new Property();
		$form = $this->createForm(PropertyType::class, $property);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$this->em->persist($property);
			$this->em->flush();
			$this->addFlash('success', 'Création effectuée avec succès');
			return $this->redirectToRoute('admin.property.index');
		}
		return $this->render('admin/property/new.html.twig', [
			'property' => $property,
			'form' => $form->createView(),
			'courrent_menu' => 'administration',
		]);
	}

	/**
	* @Route("/admin/property/{id}", name="admin.property.edit", methods="GET|POST")
	* @return Response
	*/
	public function edit(Property $property, Request $request): Response {
		$form = $this->createForm(PropertyType::class, $property);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$this->em->flush();
			$this->addFlash('success', 'Modication effectuée avec succès');
			return $this->redirectToRoute('admin.property.index');
		}
		return $this->render('admin/property/edit.html.twig', [
			'property' => $property,
			'form' => $form->createView(),
			'courrent_menu' => 'administration',
		]);
	}

	/**
	* @Route("/admin/property/{id}", name="admin.property.delete", methods="DELETE")
	* @return Response
	*/
	public function delete(Property $property, Request $request): Response {
		if($this->isCsrfTokenValid('delete' . $property->getId(), $request->get('_token'))) {
			$this->em->remove($property);
			$this->em->flush();
			$this->addFlash('success', 'Suppression effectuée avec succès');
			return $this->redirectToRoute('admin.property.index');
        } else {
        	throw new InvalidCsrfTokenException('Invalid CSRF token');
        }
		
	}

}
	