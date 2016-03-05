<?php

namespace Alx\TestTaskBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Alx\TestTaskBundle\Entity\Document;
use Alx\TestTaskBundle\Entity\Attachment;
use Alx\TestTaskBundle\Form\AttachmentType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class AttachmentsController extends Controller
{
    public function indexAction(Document $document)
    {
        $attachments = $document->getAttachments()->getValues();

        foreach ($attachments as $attachment) {
            $attachment->setPath($this->generateUrl('attachments_show', ['id' => $attachment->getId()]));
        }

        return new JsonResponse($attachments);
    }

    public function newAction(Request $request, Document $document)
    {
        $attachment = new Attachment();
        $form = $this->createForm(AttachmentType::class, $attachment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $query = $em->getRepository(Attachment::class)
                        ->createQueryBuilder('a')
                        ->select('MAX(a.order)')
                        ->where('a.document = :document')
                        ->setParameter('document', $document)
                        ->getQuery();
            $max_order = $query->getSingleScalarResult();

            $attachment->setOrder($max_order !== null ? $max_order + 1 : 0);
            $attachment->setDocument($document);

            $em->persist($attachment);
            $em->flush();

            return new Response("", Response::HTTP_CREATED);
        }

        return new Response("", Response::HTTP_BAD_REQUEST);
    }

    public function showAction(Attachment $attachment)
    {
        $response = new BinaryFileResponse($attachment->getFilePath());

        $response->headers->set('Content-Type', $attachment->getMimeType());
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $attachment->getName()
        );

        return $response;
    }

    public function deleteAction(Request $request, Attachment $attachment)
    {
        $form = $this->createDeleteForm($attachment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $query = $em->getRepository(Attachment::class)
                        ->createQueryBuilder('a')
                        ->update()
                        ->set('a.order', 'a.order - 1')
                        ->where('a.document = :document')
                        ->andWhere('a.order > :order')
                        ->setParameter('document', $attachment->getDocument())
                        ->setParameter('order', $attachment->getOrder())
                        ->getQuery();
            $query->execute();

            $em->remove($attachment);
            $em->flush();

            return new Response("", Response::HTTP_OK);
        }

        return new Response("", Response::HTTP_BAD_REQUEST);
    }

    public function reorderAction(Request $request, Attachment $attachment)
    {
        $old_order = $attachment->getOrder();

        $form = $this->createForm(AttachmentType::class, $attachment, ['method' => 'PUT', 'csrf_protection' => false]);
        $form->handleRequest($request);

        $new_order = $attachment->getOrder();

        if ($new_order == $old_order) {
            return new Response("", Response::HTTP_OK);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $query = $em->getRepository(Attachment::class)
                        ->createQueryBuilder('a')
                        ->update()
                        ->set('a.order', 'a.order ' . ($new_order > $old_order ? '-' : '+') . ' 1')
                        ->where('a.document = :document')
                        ->andWhere('a.order >= :order_lower')
                        ->andWhere('a.order <= :order_upper')
                        ->setParameter('document', $attachment->getDocument())
                        ->setParameter('order_lower', $new_order > $old_order ? $old_order : $new_order)
                        ->setParameter('order_upper', $new_order > $old_order ? $new_order : $old_order)
                        ->getQuery();
            $query->execute();

            $em->persist($attachment);
            $em->flush();

            return new Response("", Response::HTTP_OK);
        }

        return new Response("", Response::HTTP_BAD_REQUEST);
    }

    /**
     * Creates a form to delete a Attachment entity.
     * @param Attachment $attachment The Attachment entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Attachment $attachment)
    {
        return $this->createFormBuilder(null, ['csrf_protection' => false])
                    ->setAction($this->generateUrl('attachments_delete', ['id' => $attachment->getId()]))
                    ->setMethod('DELETE')
                    ->add('delete', SubmitType::class)
                    ->getForm();
    }
}
