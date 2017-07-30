<?php

namespace AppBundle\Model;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\Common\Collections\ArrayCollection;

class User
{
    const minX = 0;
    const minY = 0;
    const maxX = 9;
    const maxY = 9;

    private $session;

    public function __construct(SessionInterface &$session)
    {
        $this->session = $session;
        $this->init();
    }

    private function init()
    {
        $keys = [
            'position.x' => 0,
            'position.y' => 0,
            'position.id' => 0,
            'position.move' => 0,
            'equipment' => 'a:0:{}',
        ];

        foreach ($keys as $key => $val) {
            if(!$this->session->has($key)) {
                $this->session->set($key,$val);
            }
        }
    }

    /*
     * ID = 10 x + y
     * x = ID / 10
     * y = ID % 10
     */
    private function movePossible(SessionInterface $session, $id)
    {

        if(
            abs($session->get('position.x') - (int) ($id / 10)) <= 1 &&
            abs($session->get('position.y') - $id % 10) <= 1 &&
            (int) ($id / 10) >= self::minX &&
            $id % 10 >= self::minY &&
            (int) ($id / 10) <= self::maxX &&
            $id % 10 <= self::maxX
        ) {
            return true;
        }
        return false;
    }

    public function go($id)
    {
        $this->session->set('position.move',
            $this->session->get('position.move')+1);

        if(!$this->movePossible($this->session,$id)) {
            throw new \InvalidArgumentException("Move not allowed");
        }

        $this->session->set('position.x',(int) ($id / 10));
        $this->session->set('position.y',$id % 10);
        $this->session->set('position.id',$id);
    }

    public function getMove()
    {
        return $this->session->get('position.move');
    }


    public function getPositionId()
    {
        return $this->session->get('position.id');
    }


    public function getPositionX()
    {
        return $this->session->get('position.x');
    }

    public function getPositionY()
    {
        return $this->session->get('position.y');
    }


    public function removeEquipment($name) {
        $equipment = unserialize($this->session->get('equipment'));
        $equipment = new ArrayCollection($equipment);
        $equipment->remove($name);
        $equipment = $equipment->toArray();
        $equipment = serialize($equipment);
        $this->session->set('equipment',$equipment);
    }

    public function addEquipment($name) {
        $equipment = unserialize($this->session->get('equipment'));
        $equipment = new ArrayCollection($equipment);
        $equipment->add($name);
        $equipment = $equipment->toArray();
        $equipment = serialize($equipment);
        $this->session->set('equipment',$equipment);
    }

    public function getEquipment()
    {
        return unserialize($this->session->get('equipment'));
    }
}