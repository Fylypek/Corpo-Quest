<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use AppBundle\Model\User;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="game")
     */
    public function indexAction(Request $request)
    {
        $session = $request->getSession();
        $user = new User($session); // only for session construct
        $field = $this->getDoctrine()
            ->getRepository('AppBundle:Field')
            ->find($user->getPositionId());
        return $this->render(':default:index.html.twig',[
            'user' => $user,
            'field' => $field
        ]);
    }


    /**
     * @Route("/go/{id}", name="go")
     */
    public function goAction(Request $request, $id)
    {
        $session = $request->getSession();
        $user = new User($session); // only for session construct
        try {
            $user->go($id);
        } catch (\Exception $e) {
            return $this->render(':default:error.html.twig', [
                'user'=>$user,
                'field'=> null,
                'error'=>$e
            ]);
        }
        return $this->redirectToRoute('game');
    }
    /*
     * @Route("/pick/{id}", name="pickup")
     */
    public function pickUpAction(Request $request, $id)
    {
        $session = $request->getSession();
        $user = new User($session);
        
        $field = $this->getDoctrine()
                      ->getRepository('AppBundle:Field')
                      ->find($user->getPositionId());
        $itemsArr = $field->find($field->getItems());
        vardump($itemsArr);die;
    }
}
