<?php

class Participant {
    private $id;
    private $fullName;
    private $email;
    private $phone;
    private $eventId;
    private $registeredAt;

    public function __construct($id = 0, $fullName = '', $email = '', $phone = '', $eventId = 0, $registeredAt = '') {
        $this->id           = $id;
        $this->fullName     = $fullName;
        $this->email        = $email;
        $this->phone        = $phone;
        $this->eventId      = $eventId;
        $this->registeredAt = $registeredAt;
    }

    // Getters
    public function getId()           { return $this->id; }
    public function getFullName()     { return $this->fullName; }
    public function getEmail()        { return $this->email; }
    public function getPhone()        { return $this->phone; }
    public function getEventId()      { return $this->eventId; }
    public function getRegisteredAt() { return $this->registeredAt; }

    // Setters
    public function setId($id)                     { $this->id = $id; }
    public function setFullName($fullName)         { $this->fullName = $fullName; }
    public function setEmail($email)               { $this->email = $email; }
    public function setPhone($phone)               { $this->phone = $phone; }
    public function setEventId($eventId)           { $this->eventId = $eventId; }
    public function setRegisteredAt($registeredAt) { $this->registeredAt = $registeredAt; }
}
?>