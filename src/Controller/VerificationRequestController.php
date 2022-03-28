<?php

namespace App\Controller;

use App\Entity\VerificationRequest;
use App\Form\VerificationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VerificationRequestController extends AbstractController
{
    #[Route('/user/verification/request', name: 'app_verification_request')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $verificationRequest = new VerificationRequest();
        $form = $this->createForm(VerificationFormType::class, $verificationRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $verificationRequest->setCreatedAt(new \DateTimeImmutable());
            $verificationRequest->setStatus('Verification requested');
            $verificationRequest->setUser($this->getUser());

            $entityManager->persist($verificationRequest);
            $entityManager->flush();
        }

        return $this->render('verification_request/index.html.twig', [
            'verificationForm' => $form->createView()
        ]);
    }

    #[Route('/user/verification/request/edit/{id}', name: 'app_verification_request_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $verificationRequest = $entityManager->getRepository('App:VerificationRequest')->findOneById($id);
        $form = $this->createForm(VerificationFormType::class, $verificationRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($verificationRequest);
            $entityManager->flush();

            return $this->redirectToRoute('app_verification_request');
        }

        return $this->render('verification_request/edit.html.twig', [
            'verificationForm' => $form->createView()
        ]);
    }


    #[Route('/admin/verification/requests', name: 'app_verification_requests')]
    public function indexAdmin(Request $request, EntityManagerInterface $entityManager): Response
    {
        $verificationRequests = $entityManager->getRepository('App:VerificationRequest')->findAll();

        return $this->render('verification_request/indexAdmin.html.twig', [
            'verificationRequests' => $verificationRequests
        ]);
    }

    #[Route('/admin/verification/approve/{id}', name: 'app_verification_approve')]
    public function approve(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $verificationRequest = $entityManager->getRepository('App:VerificationRequest')->findOneById($id);

        $verificationRequest->setStatus('Approved');
        $verificationRequest->getUser()->setRoles(['ROLE_BLOGGER']);

        $entityManager->persist($verificationRequest);
        $entityManager->flush();

        return $this->redirectToRoute('app_verification_requests');
    }

    #[Route('/admin/verification/reject/{id}', name: 'app_verification_reject')]
    public function reject(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $verificationRequest = $entityManager->getRepository('App:VerificationRequest')->findOneById($id);

        $verificationRequest->setStatus('Declined');
        $verificationRequest->setReasonIfRejected($_POST['reason']);

        $entityManager->persist($verificationRequest);
        $entityManager->flush();

        return $this->redirectToRoute('app_verification_requests');
    }
}
