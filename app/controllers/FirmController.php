<?php
use Phalcon\Mvc\Controller;
/**
 * Операторы
 */
class FirmController extends Controller
{
	public function listAction()
	{
		$this->view->pageTitle = 'Список организаций';
		$this->view->items = Firm::find();
	}
	
	public function sendAction() //отправка данных из формы
    {
        $processor= new FirmProcessor();
        $processor->send($_REQUEST);
        $this->response->redirect('firm/list');
    }
    
    public function editAction()
	{
		$this->view->pageTitle = 'Добавить организацию';
	}
}