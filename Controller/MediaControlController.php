<?php

namespace Btn\MediaBundle\Controller;

use Btn\MediaBundle\Model\MediaUploader;
use Gaufrette\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Btn\AdminBundle\Controller\AbstractControlController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Media controller.
 *
 * @Route("/media")
 */
class MediaControlController extends AbstractControlController
{
    /**
     * @Route("/", name="btn_media_mediacontrol_media_index")
     * @Route("/category/{category}", name="btn_media_mediacontrol_media_index_category")
     * @Template()
     */
    public function indexAction(Request $request, $category = null)
    {
        return $this->getListData($request);
    }

    /**
     * @Route("/new", name="btn_media_mediacontrol_media_new")
     * @Route("/new/category/{category}", name="btn_media_mediacontrol_media_new_category")
     * @Template("BtnMediaBundle:MediaControl:form.html.twig")
     */
    public function newAction(Request $request, $category = null)
    {
        $form = $this->get('btn_media.adapter')->createForm($request);

        return array('form' => $form->createView(), 'entity' => null);
    }

    /**
     * @Route("/edit/{id}", name="btn_media_mediacontrol_media_edit")
     * @Template("BtnMediaBundle:MediaControl:form.html.twig")
     **/
    public function editAction(Request $request, $id)
    {
        $entity   = $this->get('btn_media.provider.media')->getRepository()->find($id);
        $form     = $this->get('btn_media.adapter')->createForm($request, $entity);

        return array('form' => $form->createView(), 'entity' => $entity);
    }

    /**
     * @Route("/upload/{id}", name="btn_media_mediacontrol_media_upload")
     * @Template("BtnMediaBundle:MediaControl:form.html.twig")
     **/
    public function uploadAction(Request $request, $id = null)
    {
        /** @var Media $entity */
        $entity = $id ? $this->get('btn_media.provider.media')->getRepository()->find($id) : null;
        /** @var Gaufrette/Filesystem $entity */
        $filesystem = $this->get('knp_gaufrette.filesystem_map')->get('btn_media');
        /** @var MediaUploader $uploader */
        $uploader = $this->get('btn_media.uploader');
        $uploader->setFilesystem($filesystem);
        $adapter = $this->get('btn_media.adapter');
        $form    = $adapter->createForm($request, $entity);
        $uploader->setAdapter($adapter);

        if ($request->isXmlHttpRequest()) {
            return $this->json(array(
                'success' => $uploader->isSuccess()
            ));
        } else {
            $medias = $uploader->getUploadedMedias();
            $params = array();
            $media  = null;
            //if there was a single upload, and file was added to the category, set GET category id param
            if (is_array($medias) && count($medias) === 1) {
                $media = array_pop($medias);
                if ($media && $media->getCategory()) {
                    $params = array('category' => $media->getCategory()->getId());
                }
            }
            if (count($medias) > 0 || $media) {

                return $this->redirect($this->generateUrl('btn_media_mediacontrol_media_index_category', $params));
            }
        }

        return array('form' => $form->createView(), 'entity' => null);
    }

    /**
     * @Route("/delete/{id}", name="btn_media_mediacontrol_media_delete")
     **/
    public function deleteAction(Request $request, $id)
    {
        $params = array();
        try {
            $provider = $this->get('btn_media.provider.media');
            $entity   = $provider->getRepository()->find($id);
            if ($entity->getCategory()) {
                $params['category'] = $entity->getCategory()->getId();
            }
            $provider->delete($entity);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());

        }

        return $this->redirect($this->generateUrl(empty($params) ? 'btn_media_mediacontrol_media_index' : 'btn_media_mediacontrol_media_index_category', $params));
    }

    /**
     * @Route("/list-modal", name="btn_media_mediacontrol_listmodal")
     * @Template()
     **/
    public function listModalAction(Request $request)
    {
        $separated = $request->get('separated');
        $data      = $this->getListData($request, true);

        $data['isModal']      = true;
        $data['isPagination'] = !$separated;
        $data['separated']    = $separated;

        return $data;
    }

    /**
     * @Route("/list-modal-content", name="btn_media_mediacontrol_listmodalcontent")
     * @Template("BtnMediaBundle::_list.html.twig")
     **/
    public function listModalContentAction(Request $request)
    {
        $category             = $request->get('category');
        $data                 = $this->getListData($request, ($category == NULL));
        $data['isModal']      = true;
        $data['isPagination'] = true;
        $data['separated']    = false;

        return $data;
    }

    /**
     * @Route("/dummy-upload", name="btn_media_mediacontrol_dummyupload")
     **/
    public function dummyUploadAction()
    {
        $filesystem = $this->get('knp_gaufrette.filesystem_map')->get('btn_media');

        $file = new \Gaufrette\File('text.txt', $filesystem);
        $file->setContent('Hello World');
        die();
    }

    /**
     * Get paginated media list
     */
    private function getListData(Request $request)
    {
        $category      = $request->get('category');
        $mediaProvider = $this->get('btn_media.provider.media');
        $method        = $category ? 'findByCategory' : 'findAll';
        $entities      = $mediaProvider->getRepository()->$method($category);

        /* @todo: number of mediafiles per page - to bundle config */
        $pagination = $this->get('knp_paginator')->paginate($entities, $request->get('page', 1), 6);

        return array('pagination' => $pagination);
    }
}
