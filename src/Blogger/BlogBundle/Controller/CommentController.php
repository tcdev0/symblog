<?php
// src/Blogger/BlogBundle/Controller/CommentController.php

namespace Blogger\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Blogger\BlogBundle\Entity\Comment;
use Blogger\BlogBundle\Form\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 *  Comment Controller
 */
class CommentController extends Controller
{
    /**
     * @param $blog_id
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("{blog_id}" , name="comment_new")
     * @Method("GET")
     * @Template("BloggerBlogBundle:Comment:form.html.twig")
     */
    public function newAction($blog_id)
    {
        $blog = $this->getBlog($blog_id);

        $comment = new Comment();
        $comment->setBlog($blog);
        $form   = $this->createForm(new CommentType(), $comment);

        return array(
                'comment' => $comment,
                'form'   => $form->createView()
            );
    }

    /**
     * @param $blog_id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("{blog_id}" , name="comment_create")
     * @Method("POST")
     * @Template("BloggerBlogBundle:Comment:create.html.twig")
     */
    public function createAction($blog_id)
    {
        $blog = $this->getBlog($blog_id);

        $comment  = new Comment();
        $comment->setBlog($blog);

        $request = $this->getRequest();
        $form    = $this->createForm(new CommentType(), $comment);

        /*if ($this->getRequest()->isMethod('POST')) {*/
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()
                           ->getManager();
                $em->persist($comment);
                $em->flush();

                return $this->redirect($this->generateUrl('blog_show', array(
                    'id'   => $comment->getBlog()->getId() ,
                    'slug' => $comment->getBlog()->getSlug())).
                    '#comment-' . $comment->getId()
                );
            }
        //}

        return array(
            'comment' => $comment,
            'form'    => $form->createView()
        );
    }

    /**
     * @param $blog_id
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getBlog($blog_id)
    {
        $em = $this->getDoctrine()
            ->getManager();

        $blog = $em->getRepository('BloggerBlogBundle:Blog')->find($blog_id);

        if (!$blog) {
            throw $this->createNotFoundException('Unable to find Blog post.');
        }

        return $blog;
    }

}
