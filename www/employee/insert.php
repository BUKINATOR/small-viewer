<?php
require_once "../../bootstrap/bootstrap.php";

class EmployeeInsertPage extends CRUDPage
{
    public string $title = "Založit nového zaměstnance";
    protected int $state;
    private Employee $employee;
    private array $errors;

    protected function prepareData(): void
    {
        parent::prepareData();
        $this->state = $this->getState();

        switch ($this->state) {
            case self::STATE_FORM_REQUEST:
                $this->employee = new Employee();
                $this->errors = [];
                break;

            case self::STATE_DATA_SENT:
                // load data
                $this->employee = Employee::readPost();
                // validate data
                $this->errors = [];
                if ($this->employee->validate($this->errors)) {
                    // process
                    $result = $this->employee->insert();
                    // redirect
                    $this->redirect(self::ACTION_INSERT, $result);
                } else {
                    // back to form
                    $this->state = self::STATE_FORM_REQUEST;
                }
                break;
        }
    }

    protected function pageBody(): string
    {
        $rooms = Room::all();

        return MustacheProvider::get()->render("EmployeeForm",
            [
                'employee' => $this->employee,
                'rooms' => $rooms,
                'edit' => false,
                'errors' => $this->errors
            ]);
    }

    protected function getState(): int
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
            return self::STATE_DATA_SENT;

        return self::STATE_FORM_REQUEST;
    }

}

$page = new EmployeeInsertPage();
$page->render();
?>