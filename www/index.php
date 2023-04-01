<?php
require_once "../bootstrap/bootstrap.php";

class IndexPage extends Page
{
    public string $title = "Prohlížeč databáze";

    protected function pageBody(): string
    {
        return "";
    }
}

$page = new IndexPage();
$page->render();

?>
