<?php

require_once '../../bootstrap/bootstrap.php';

class EmployeeListPage extends CRUDPage
{
    public string $title = 'Seznam pracovníků';

    protected function pageBody(): string
    {
        $isAdmin = $_SESSION['employee']->admin ?? false;
        $html = $this->renderAlert();
        $employees = Employee::all();
        $html .= MustacheProvider::get()->render('EmployeeList', [
            'employees' => $employees,
            'isAdmin' => $isAdmin,
        ]);
        return $html;
    }

    private function renderAlert(): string
    {
        $action = filter_input(INPUT_GET, 'action');
        if (!$action) {
            return '';
        }

        $success = filter_input(INPUT_GET, 'success', FILTER_VALIDATE_INT);
        $data = [];

        switch ($action) {
            case self::ACTION_INSERT:
                $data['message'] = $success === 1 ? 'Pracovník byl úspěšně vytvořen.' : 'Při vytváření pracovníka došlo k chybě.';
                $data['alertType'] = $success === 1 ? 'success' : 'danger';
                break;

            case self::ACTION_DELETE:
                $data['message'] = $success === 1 ? 'Pracovník byl úspěšně smazán.' : 'Při mazání pracovníka došlo k chybě.';
                $data['alertType'] = $success === 1 ? 'success' : 'danger';
                break;

            default:
                return '';
        }

        return MustacheProvider::get()->render('alert', $data);
    }
}

$page = new EmployeeListPage();
$page->render();