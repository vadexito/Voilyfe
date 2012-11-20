<?php
class Form
{
    public $a=4;
}
class Model
{
public $form;

public function __construct()
{
    $this->form = new Form();
}

public function getForm($f)
{
$f->a=10;
}
}

$model = new Model();
$model ->getForm($model->form);

echo $model->form->a;

?>