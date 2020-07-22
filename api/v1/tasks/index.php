<?php

namespace App\Api\V1;

use App\Db\Connection;
use App\Repositories;

include_once '../../../index.php';

$tasks = new Tasks();
$tasks->{[
    'POST' => 'create',
    'GET' => 'read',
    'PUT' => 'update',
    'DELETE' => 'delete'
][$_SERVER["REQUEST_METHOD"]]}();

class Tasks {
    public $repTasks;
    public $repTags;
    public $repTasksTags;

    function __construct() {
        $db = Connection::getInstance()->getConnection();

        $this->repTasks = new Repositories\Tasks($db);
        $this->repTags = new Repositories\Tags($db);
        $this->repTasksTags = new Repositories\TasksTags($db);

        $this->repTasks->fill();
        $this->repTags->fill();
        $this->repTasksTags->fill();
    }

    function create() {
        echo json_encode($this->repTasks->create([
            ':name' => htmlspecialchars($_POST['name']),
            ':description' => htmlspecialchars($_POST['description']),
            ':date' => date(\DateTime::ISO8601)
        ]));
    }

    function read() {
        $id = htmlspecialchars($_GET['id']);

        if ($id) {
            $tasksTags = $this->repTasksTags->readAll([
                'taskId' => $id
            ]);

            $tagsIds = array_map(function($item) {
                return $item['tagId'];
            }, $tasksTags);

            echo json_encode($this->repTasks->read([
                'id' => $id
            ], [
                'tags' => $this->repTags->readAll([
                    'ids' => $tagsIds
                ])
            ]));
        } else {
            echo json_encode($this->repTasks->readAll([]));
        }
    }

    function update() {
        echo 'update data';
    }

    function delete() {
        echo 'delete data';
    }
}