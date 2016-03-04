<?php

namespace Alx\TestTaskBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Alx\TestTaskBundle\Entity\Document;
use Alx\TestTaskBundle\Entity\Attachment;
use Alx\TestTaskBundle\Form\DocumentType;
use Alx\TestTaskBundle\Form\AttachmentType;

/**
 * Documents controller.
 */
class DocumentsController extends Controller
{
    /**
     * Lists all Document entities.
     * @return Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $documents = $em->getRepository(Document::class)->findAll();

        return $this->render('@AlxTestTask/documents/index.html.twig', [
            'documents' => $documents
        ]);
    }

    /**
     * Creates a new Document entity.
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($document);
            $em->flush();

            $this->addFlash('success', 'Document has been successfully created');

            return $this->redirectToRoute('documents_index');
        }

        return $this->render('@AlxTestTask/documents/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Displays a form to edit an existing Document entity.
     * @param Request $request
     * @param Document $document
     * @return Response
     */
    public function editAction(Request $request, Document $document)
    {
        $form = $this->createForm(DocumentType::class, $document, ['method' => 'PUT']);
        $form->handleRequest($request);

        $attachment = new Attachment();
        $attachment_form = $this->createForm(AttachmentType::class, $attachment, [
            'action' => $this->generateUrl('attachments_new', ['id' => $document->getId()])
        ]);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($document);
            $em->flush();

            $this->addFlash('success', 'Document has been successfully modified');

            return $this->redirectToRoute('documents_index');
        }

        return $this->render('@AlxTestTask/documents/edit.html.twig', [
            'form' => $form->createView(),
            'attachment_form' => $attachment_form->createView()
        ]);
    }

    /**
     * Deletes a Document entity.
     * @param Request $request
     * @param Document $document
     * @return Response
     */
    public function deleteAction(Request $request, Document $document)
    {
        $form = $this->createDeleteForm($document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($document);
            $em->flush();

            $this->addFlash('success', 'Document has been successfully removed');
        }

        return $this->redirectToRoute('documents_index');
    }

    /**
     * Creates a form to delete a Document entity.
     * @param Document $document The Document entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Document $document)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('documents_delete', ['id' => $document->getId()]))
                    ->setMethod('DELETE')
                    ->getForm();
    }
}
