<?php
require_once "../../bootstrap/bootstrap.php";
include "../../models/Key.php";
include "../../models/Room.php";



class EmployeeDetailPage extends Page
{
//    public string $title = "Seznam místností";
    private $employee;
    private $keys;

    protected function prepareData(): void
    {
        parent::prepareData();

        //na koho se ptá (příp chyba)
        $employee_id = filter_input(INPUT_GET, 'employee_id', FILTER_VALIDATE_INT);

        if (!$employee_id){
            throw new BadRequestException();
        }

        $this->keys = $this->getKeys($employee_id);

        //vytáhnu místnost
        $this->employee = Employee::findByID($employee_id);

        //mám ho? (příp chyba)
        if (!$this->employee){
            throw new NotFoundException();
        }

        $this->title = htmlspecialchars( "Člověk {$this->employee->name} ({$this->employee->surname})" );

        //získám lidi
    }

    protected function pageBody(): string
    {
        //ukážu místnost
        return MustacheProvider::get()->render("EmployeeDetail", ["employee" => $this->employee, 'keys' => $this->keys]);
    }

    protected function getKeys($id) : array
    {
        $keys = Key::findByEmployeeId($id);
        $keys = array_map(function ($key) {
            $key['room_name'] = Key::getRoomNameById($key['room']);
            return $key;
        }, $keys);
        return $keys;
    }

}

$page = new EmployeeDetailPage();
$page->render();