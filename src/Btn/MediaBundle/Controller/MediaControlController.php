<?php

namespace Btn\MediaBundle\Controller;

use Btn\MediaBundle\Model\MediaUploader;
use Gaufrette\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Btn\AdminBundle\Controller\AbstractControlController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Btn\AdminBundle\Annotation\EntityProvider;

/**
 * @Route("/media")
 * @EntityProvider()
 */
class MediaControlController extends AbstractControlController
{
    /**
     * @Route("/", name="btn_media_mediacontrol_media_index")
     * @Route("/category/{category}",
     *    name="btn_media_mediacontrol_media_index_category",
     *    requirements={"category" = "\d+"},
     * )
     * @Template()
     */
    public function indexAction(Request $request)
    {
        return $this->getListData($request);
    }

    /**
     * @Route("/new", name="btn_media_mediacontrol_media_new")
     * @Route("/new/category/{category}",
     *     name="btn_media_mediacontrol_media_new_category",
     *     requirements={"category" = "\d+"},
     *     methods={"GET"},
     * )
     * @Template("BtnMediaBundle:MediaControl:form.html.twig")
     */
    public function newAction(Request $request)
    {
        $form = $this->get('btn_media.adapter')->createForm($request);

        return array(
            'form'   => $form->createView(),
            'entity' => null,
        );
    }

    /**
     * @Route("/edit/{id}",
     *     name="btn_media_mediacontrol_media_edit",
     *     requirements={"id" = "\d+"},
     *     methods={"GET"},
     * )
     * @Template("BtnMediaBundle:MediaControl:form.html.twig")
     **/
    public function editAction(Request $request, $id)
    {
        $entity   = $this->getEntityProvider()->getRepository()->find($id);
        $form     = $this->get('btn_media.adapter')->createForm($request, $entity);

        return array(
            'form'   => $form->createView(),
            'entity' => $entity,
        );
    }

    /**
     * @Route("/upload/{id}",
     *     name="btn_media_mediacontrol_media_upload",
     *     requirements={"id" = "\d+"},
     *     methods={"POST"},
     * )
     * @Template("BtnMediaBundle:MediaControl:form.html.twig")
     **/
    public function uploadAction(Request $request, $id = null)
    {
        $ep = $this->getEntityProvider();
        /** @var Media $entity */
        $entity = $id ? $ep->getRepository()->find($id) : $ep->create();
        /** @var Gaufrette/Filesystem $entity */
        $filesystem = $this->get('knp_gaufrette.filesystem_map')->get('btn_media');
        /** @var \Btn\MediaBundle\AdapterInterface $adapter */
        $adapter = $this->get('btn_media.adapter');
        $form    = $adapter->createForm($request, $entity);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var MediaUploader $uploader */
            $uploader = $this->get('btn_media.uploader');
            $uploader->setFilesystem($filesystem);
            $uploader->setAdapter($adapter);
            $uploader->handleUpload();

            if (count($uploader->getUploadedMedias()) > 0) {
                foreach ($uploader->getUploadedMedias() as $media) {
                    $ep->save($media);
                }
            } else {
                $ep->save($entity);
            }

            if ($request->isXmlHttpRequest()) {
                return $this->json(array(
                    'success' => $uploader->isSuccess(),
                ));
            } else {
                $medias = $uploader->getUploadedMedias();
                if (count($medias) > 0 && !$id) {
                    $id = array_pop($medias)->getId();
                }

                if ($id) {
                    return $this->redirect($this->generateUrl(
                        'btn_media_mediacontrol_media_edit',
                        array('id' => $id)
                    ));
                }
            }
        }

        return array(
            'form'   => $form->createView(),
            'entity' => $entity,
        );
    }

    /**
     * @Route("/delete/{id}/{csrf_token}",
     *     name="btn_media_mediacontrol_media_delete",
     *     requirements={"id" = "\d+"},
     *     methods={"GET"},
     * )
     **/
    public function deleteAction(Request $request, $id, $csrf_token)
    {
        $this->validateCsrfTokenOrThrowException('btn_media_mediacontrol_media_delete', $csrf_token);

        $params = array();
        try {
            $provider = $this->getEntityProvider();
            $entity   = $provider->getRepository()->find($id);
            if ($entity->getCategory()) {
                $params['category'] = $entity->getCategory()->getId();
            }
            $provider->delete($entity);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return $this->redirect($this->generateUrl(
            empty($params) ? 'btn_media_mediacontrol_media_index' : 'btn_media_mediacontrol_media_index_category',
            $params
        ));
    }

    /**
     * @Route("/modal", name="btn_media_mediacontrol_modal")
     * @Template("BtnMediaBundle:MediaModal:modal.html.twig")
     **/
    public function modalAction(Request $request)
    {
        $request->attributes->set('modal', true);

        $data = $this->getListData($request);
        if ($request->get('CKEditor')) {
            return $this->render('BtnMediaBundle:MediaModal:cke.html.twig', $data);
        }

        return $data;
    }

    /**
     * @Route("/modal-content", name="btn_media_mediacontrol_modalcontent")
     * @Route("/modal-content/{category}",
     *     name="btn_media_mediacontrol_modalcontent_category",
     *     requirements={"category" = "\d+"}
     * )
     * @Template("BtnMediaBundle:MediaModal:_content.html.twig")
     **/
    public function modalContentAction(Request $request)
    {
        $request->attributes->set('modal', true);

        return $this->getListData($request);
    }

    private function generateFilterFormUrl(Request $request) {
        $category = $request->get('category');
        if ($category) {
            return $this->generateUrl('btn_media_mediacontrol_media_index_category', array('category' => $category));
        }

        return $this->generateUrl('btn_media_mediacontrol_media_index');
    }

    /**
     * Get paginated media list
     */
    private function getListData(Request $request, $perPage = 6)
    {
        $category = $request->get('category');
        $group = $request->get('group');
        $filter = $this->get('btn_media.filter.media');

        $filterForm = $filter->createForm(array(
            'category' => $request->attributes->get('category')
        ), array(
            'category_field_hidden' => $category,
            'media_group_field_hidden' => $request->attributes->has('modal'),
            'action' => $this->generateFilterFormUrl($request),
        ));

        if ($group) {
            $filterForm->get('group')->setData($group);
        }

        if ($filter->applyFilters()) {
            $entities = $filter->getQuery();
        } else {
            $repository = $this->getEntityProvider()->getRepository();
            if ($category) {
                $customMethod = 'findByCategoryForCrudIndex';
                $defaultMethod = 'findByCategory';
            } else {
                $customMethod = 'findAllForCrudIndex';
                $defaultMethod = 'findAll';
            }
            $method = method_exists($repository, $customMethod) ? $customMethod : $defaultMethod;
            $entities = $repository->$method($category);
        }

        $filterOriginal = $this->get('service_container')->getParameter('btn_media.media.imagine.filter_original');

        /* @todo: number of mediafiles per page - to bundle config */
        $pagination = $this->paginate($entities, null, $perPage);

        return array(
            'pagination' => $pagination,
            'filter_form' => $filterForm ? $filterForm->createView() : null,
            'filter_original' => $filterOriginal,
            'group' => $request->get('group'),
        );
    }
}
