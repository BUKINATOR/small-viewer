<?php
require_once "../../bootstrap/bootstrap.php";

class EmployeeDeletePage extends CRUDPage
{

    protected function prepareData(): void
    {
        parent::prepareData();
        $employee_id = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        if (!$employee_id)
            throw new BadRequestException();

        $result = Room::deleteById($employee_id);
        $this->redirect(self::ACTION_DELETE, $result);
    }


    protected function pageBody(): string
    {
        return "";
    }
}

$page = new EmployeeDeletePage();
$page->render();