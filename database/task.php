<?php 
declare(strict_types = 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/bdmanage.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/response.php';

class Task extends BD {
    public function create_task ($data) {
        $task = new BD($data);
        $response = $task->insert('tasks', 'id');
        if ($response) {
            http_response(200, json_encode([
                "message" => "The task has been created successfully"
            ]));
        } else {
            http_response(500, http_response(200, json_encode([
                "message" => "we can fetch teams, sorry!"
            ])));
        }
    }

    public function update_task ($data, $params) {
        $role = new BD(["user_id" => $params['user_id'], "team_id" => $params['team_id']]);
        $response = $role->get('roles', "role");
        $role_response = json_decode(json_encode($response), true);
        if ($role_response[0]['role'] === 'administrator' || $role_response[0]['role'] === 'author') {
            $task = new BD($data);
            $res = $task->update('tasks', ['id' => $params['task_id']]);
            if ($res) {
                http_response(200, json_encode([
                    "message" => "The task has been updated successfully"
                ]));
            } else {
                http_response(500,http_response(200, json_encode([
                    "message" => "sorry we can't update the task, sorry!"
                ])));
            }
        } else {
            http_response(403,json_encode([
                "message" => "you not have permission to make this action!", 
            ]));
        }
    }

    public function delete_task_cascade ($id) {
            $params = ['id' => (int)$id];
            $params_schedule = ['task_id' => (int)$id];
            // recuperer la colonne que on vx supprimer
            $schedules = new BD($params_schedule);
            $response_schedules = $schedules->search('schedules', 'calendar_id', 'task_id');
            // recuperer la colonne que on vx supprimer
            $params_calendar = ['calendar_id' => $response_schedules['calendar_id']];
            $calendar = new BD($params_calendar);
            // supprime la ligne de la table schedules
            $task = new BD($id);
            $response_deleted_schedule = $task->delete('schedules', $params_schedule);
            if ($response_deleted_schedule && $response_schedules) {
                $calendars = $calendar->search('schedules', '*', 'calendar_id');
                if (!$calendars) {
                    $response_deleted_calendar = $calendar->delete('calendar', [
                        'id' => $response_schedules['calendar_id'],
                    ]);
                    if (!$response_deleted_calendar) {
                        http_response(500, "failed delete calendar");
                    }
                }
                $response = $task->delete('tasks', $params);
                if ($response) {
                    http_response(201, "The task has been deleted successfully", "Deleted");
                } else {
                    http_response(500, "failed delete calendar");
                }
            } else {
                http_response(500, "failed delete task");
            }
    }
}

?>