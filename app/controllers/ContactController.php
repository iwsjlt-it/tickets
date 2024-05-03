<?php
use Phalcon\Mvc\Controller;
/**
 * Контакты
 */
class ContactController extends Controller
{
	public function listAction()
	{
		$this->view->pageTitle = 'Список контактных лиц';
		$this->view->items = Contact::find();
		$this->view->firms = Firm::find();
	}
	
	public function editAction($id)
	{
		$this->view->pageTitle = 'Редактирование контактного лица';
		$this->view->item = Contact::findFirstById($id);
		$this->view->firms = Firm::find();
	}
	
	public function updateAction($id)
	{
	    $processor= new ContactProcessor();
        $processor->update($id ,$_POST);
        $this->response->redirect('contact/list');
	}
}