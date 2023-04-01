<?php

require_once __DIR__ . "/../../bootstrap/bootstrap.php";
include __DIR__ . "/../../models/Key.php";
include __DIR__ . "/../../models/Room.php";

class EmployeeInsertPage extends CRUDPage
{
    public string $title = "Upravit zaměstnance";
    protected int $state;
    private Employee $employee;
    private array $errors = [];
    private array $roomKeys = [];
    private array $rooms = [];

    protected function prepareData(): void
    {
        parent::prepareData();
        $this->state = $this->getState();

        switch ($this->state) {
            case self::STATE_FORM_REQUEST:
                $employee_id = filter_input(INPUT_GET, 'employee_id', FILTER_VALIDATE_INT);
                if (!$employee_id) {
                    throw new BadRequestException();
                }

                $this->roomKeys = $this->rooms = Room::all();
                $this->employee = Employee::findByID($employee_id);
                if (!$this->employee) {
                    throw new NotFoundException();
                }
                break;

            case self::STATE_DATA_SENT:
                $this->employee = Employee::readPost();
                $this->errors = [];
                if ($this->employee->validate($this->errors)) {
                    $result = $this->employee->update();
                    $this->redirect(self::ACTION_UPDATE, $result);
                } else {
                    $this->state = self::STATE_FORM_REQUEST;
                }
                break;
        }
    }

    protected function pageBody(): string
    {
        return MustacheProvider::get()->render("EmployeeForm", [
            'employee' => $this->employee,
            'errors' => $this->errors,
            'keys' => $this->roomKeys,
            'rooms' => $this->rooms,
        ]);
    }

    protected function getState(): int
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST' ? self::STATE_DATA_SENT : self::STATE_FORM_REQUEST;
    }

    protected function getKeys($id): array
    {
        $keys = Key::findByEmployeeId($id);
        return array_map(function ($key) {
            $key['room_name'] = Key::getRoomNameById($key['room']);
            return $key;
        }, $keys);
    }
}

$page = new EmployeeInsertPage();
$page->render();