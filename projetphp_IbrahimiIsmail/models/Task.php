<?php
class Task {
    public $id;
    public $title;
    public $description;
    public $isCompleted;
    public $createdAt;

    public function __construct($id, $title, $description, $isCompleted, $createdAt) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->isCompleted = $isCompleted;
        $this->createdAt = $createdAt;
    }
}
