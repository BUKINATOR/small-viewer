<?php
require_once "../bootstrap/bootstrap.php";

class ChangePasswordPage extends CRUDPage
{
    public string $title = "Změnit heslo";
    protected int $state;

    protected string $oldPassword;
    protected string $newPassword;
    protected string $newPassword2;

    protected array $errors = [];




    protected function pageBody(): string
    {
        return MustacheProvider::get()->render("ChangePasswordForm", ['errors' => $this->errors]);
    }

    protected function getState(): int
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
            return self::STATE_DATA_SENT;

        return self::STATE_FORM_REQUEST;
    }

    function getInput(): void
    {
        $this->oldPassword = filter_input(INPUT_POST, 'old_password', FILTER_DEFAULT) ?? "";
        $this->newPassword = filter_input(INPUT_POST, 'new_password', FILTER_DEFAULT) ?? "";
        $this->newPassword2 = filter_input(INPUT_POST, 'new_password_again', FILTER_DEFAULT) ?? "";
    }

    function validateInput(): bool
    {
        if ($this->oldPassword !== $_SESSION["employee"]->password)
            $this->errors["old_password"] = "Staré heslo je špatně";

        if ($this->oldPassword === $this->newPassword)
            $this->errors["new_password"] = "Nové heslo nesmí být stejné jako staré";

        if ($this->newPassword !== $this->newPassword2)
            $this->errors["new_password_again"] = "Nové heslo se neshoduje s kontrolním heslem";

        return count($this->errors) === 0;
    }

    function changePassword(): bool
    {
        $employee = $_SESSION["employee"];
        $employee->password = $this->newPassword;
        return $employee->update();
    }
}

$page = new ChangePasswordPage();
$page->render();