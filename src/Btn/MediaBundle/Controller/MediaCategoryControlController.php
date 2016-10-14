<?php

namespace Btn\MediaBundle\Controller;

use Btn\AdminBundle\Controller\CrudController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Btn\AdminBundle\Annotation\Crud;

/**
 * @Route("/mediacategory")
 * @Crud(
 *     createTemplate="BtnMediaBundle:MediaCategoryControl:create.html.twig",
 *     updateTemplate="BtnMediaBundle:MediaCategoryControl:update.html.twig",
 * )
 */
class MediaCategoryControlController extends CrudController
{
    /**
     * @Route("/", methods={"GET", "POST"})
     */
    public function indexAction(Request $request)
    {
        return $this->redirect($this->generateUrl('btn_media_mediacontrol_media_index'));
    }

    /**
     * Lists all Nodes.
     *
     * @Template()
     */
    public function treeAction(Request $request)
    {
        $provider = $this->getEntityProvider();
        $repo     = $provider->getRepository();
        $current  = null;
        if ($request->get('category') !== null) {
            $current = $repo->find($request->get('category'));
        }

        $this->get('btn_base.asset_loader')->load(array('btn_admin_loading', 'btn_admin_jstree'));

        return array(
            'categories'  => $repo->findAll(),
            'currentNode' => $current,
            'modal'       => $request->get('modal'),
            'categoryLinkDefaultParams' => $this->generateCategoryLinkDefaultParams($request),
        );
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function generateCategoryLinkDefaultParams(Request $request)
    {
        $linkDefaultParams = array();
        $group = $request->get('group');
        if ($group) {
            $linkDefaultParams['group'] = $group;
        }

        return $linkDefaultParams;
    }
}
