<?php

namespace Alx\TestTaskBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Alx\TestTaskBundle\Entity\Document;
use Alx\TestTaskBundle\Form\DocumentType;

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

            return $this->redirectToRoute('documents_show', ['id' => $document->getId()]);
        }

        return $this->render('@AlxTestTask/documents/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Finds and displays a Document entity.
     * @param Document $document
     * @return Response
     */
    public function showAction(Document $document)
    {
        return $this->render('@AlxTestTask/documents/show.html.twig', [
            'document' => $document
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
        $editForm = $this->createForm(DocumentType::class, $document, ['method' => 'PUT']);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($document);
            $em->flush();

            return $this->redirectToRoute('documents_show', ['id' => $document->getId()]);
        }

        return $this->render('@AlxTestTask/documents/edit.html.twig', [
            'edit_form' => $editForm->createView()
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
