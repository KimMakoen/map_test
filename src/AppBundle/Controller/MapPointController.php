<?php

namespace AppBundle\Controller;

use AppBundle\Entity\MapPoint;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Mappoint controller.
 *
 * @Route("mappoint")
 */
class MapPointController extends Controller
{
    const SPBLATITUDE = 59.942196;
    const SPBLONGITUDE = 30.304882;
    const RADIUS = 0.4;
    /**
     * Lists all mapPoint entities.
     *
     * @Route("/{page}", name="mappoint_index", requirements={"page": "\d+"})
     * @Method("GET")
     */
    public function indexAction($page = 1)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder= $this->getDoctrine()->getRepository('AppBundle:MapPoint')->createQueryBuilder('m');
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(2);
        $pagerfanta->setCurrentPage($page);
        return $this->render('mappoint/index.html.twig', [
            'my_pager' => $pagerfanta
            ]
        );

    }

    /**
     * Creates a new mapPoint entity.
     *
     * @Route("/new", name="mappoint_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $mapPoint = new Mappoint();
        $form = $this->createForm('AppBundle\Form\MapPointType', $mapPoint);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($mapPoint);
            $em->flush();

            return $this->redirectToRoute('mappoint_show', array('id' => $mapPoint->getId()));
        }

        return $this->render('mappoint/new.html.twig', array(
            'mapPoint' => $mapPoint,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a mapPoint entity.
     *
     * @Route("/{id}", name="mappoint_show")
     * @Method("GET")
     */
    public function showAction(MapPoint $mapPoint)
    {
        $deleteForm = $this->createDeleteForm($mapPoint);

        return $this->render('mappoint/show.html.twig', array(
            'mapPoint' => $mapPoint,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing mapPoint entity.
     *
     * @Route("/{id}/edit", name="mappoint_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, MapPoint $mapPoint)
    {
        $deleteForm = $this->createDeleteForm($mapPoint);
        $editForm = $this->createForm('AppBundle\Form\MapPointType', $mapPoint);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('mappoint_edit', array('id' => $mapPoint->getId()));
        }

        return $this->render('mappoint/edit.html.twig', array(
            'mapPoint' => $mapPoint,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a mapPoint entity.
     *
     * @Route("/{id}", name="mappoint_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, MapPoint $mapPoint)
    {
        $form = $this->createDeleteForm($mapPoint);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($mapPoint);
            $em->flush();
        }

        return $this->redirectToRoute('mappoint_index');
    }

    /**
     * Creates a form to delete a mapPoint entity.
     *
     * @param MapPoint $mapPoint The mapPoint entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(MapPoint $mapPoint)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('mappoint_delete', array('id' => $mapPoint->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    /**
     * Search in circle.
     *
     * @Route("/search", name="mappoint_search")
     * @Method("GET")
     */
    public function searchInCircleAction(){
        $repository = $this->getDoctrine()->getRepository('AppBundle:MapPoint');

        $points = $repository->findAll();

        return $this->render('mappoint/search.html.twig', [
                'db_points' => $points
            ]
        );


    }
}
