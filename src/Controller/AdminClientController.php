<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/clients')]
class AdminClientController extends AbstractController
{
    #[Route('/', name: 'admin_clients')]
    public function index(ClientRepository $clientRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin_client/index.html.twig', [
            'clients' => $clientRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_clients_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, ClientRepository $clientRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $client = new Client();
        $client->setRole('ROLE_USER');

        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($clientRepository->findOneBy(['email' => $client->getEmail()])) {
                $this->addFlash('error', 'L’email est déjà utilisé.');
            } elseif ($form->isValid()) {
                $entityManager->persist($client);
                $entityManager->flush();
                $this->addFlash('success', 'Client ajouté avec succès.');

                return $this->redirectToRoute('admin_clients');
            } else {
                $this->addFlash('error', 'Veuillez corriger les erreurs du formulaire.');
            }
        }

        return $this->render('admin_client/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'admin_clients_edit')]
    public function edit(Client $client, Request $request, EntityManagerInterface $entityManager, ClientRepository $clientRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $existingClient = $clientRepository->findOneBy(['email' => $client->getEmail()]);
            if ($existingClient && $existingClient->getId() !== $client->getId()) {
                $this->addFlash('error', 'L’email est déjà utilisé.');
            } elseif ($form->isValid()) {
                $entityManager->flush();
                $this->addFlash('success', 'Client modifié avec succès.');

                return $this->redirectToRoute('admin_clients');
            } else {
                $this->addFlash('error', 'Veuillez corriger les erreurs du formulaire.');
            }
        }

        return $this->render('admin_client/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'admin_clients_delete', methods: ['POST'])]
    public function delete(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete' . $client->getId(), $request->request->get('_token'))) {
            $entityManager->remove($client);
            $entityManager->flush();
            $this->addFlash('success', 'Client supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_clients');
    }
}
