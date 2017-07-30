<?php
namespace app\models;

interface ElevatorInterface {
    public function  addCall($floor, $direction = false);
    public function  addJob($neededFloor);
}