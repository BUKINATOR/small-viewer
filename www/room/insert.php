<?php
require_once "../../bootstrap/bootstrap.php";

class RoomInsertPage extends CRUDPage
{
    public string $title = "Založit novou místnost";
    protected int $state;
    private Room $room;
    private array $errors;

    protected function prepareData(): void
    {
        parent::prepareData();
        $this->state = $this->getState();

        switch ($this->state) {
            case self::STATE_FORM_REQUEST:
                $this->room = new Room();
                $this->errors = [];
                break;

            case self::STATE_DATA_SENT:
                $this->room = Room::readPost();
                $this->errors = [];
                if ($this->room->validate($this->errors)) {
                    $result = $this->room->insert();
                    $this->redirect(self::ACTION_INSERT, $result);
                } else {
                    $this->state = self::STATE_FORM_REQUEST;
                }
                break;
        }
    }

    protected function pageBody(): string
    {
        return MustacheProvider::get()->render("room_form",
            [
                'room' => $this->room,
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

$page = new RoomInsertPage();
$page->render();