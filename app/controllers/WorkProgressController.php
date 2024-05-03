<?php
use Phalcon\Mvc\Controller;
/**
 * Прогресс
 */
class WorkProgressController extends Controller
{
	public function indexAction($id)
	{
		$this->view->pageTitle = 'Список организаций';
		$this->view->items = WorkProgress::find("ticket_id=$id");
		$this->view->ticket_id = $id;
	}
	
		public function editAction($id)
	{
		$this->view->pageTitle = 'Список организаций';
		$this->view->item = WorkProgress::findfirst("id=$id");
	}
	
	
	public function sendAction() //отправка данных из формы
    {
        $processor= new WorkProgressProcessor();
        $processor->send($_REQUEST);
        $this->response->redirect('work_progress/index');
    }
    
    	public function updateAction() //отправка данных из формы
    {
        $processor= new WorkProgressProcessor();
        $processor->update($_REQUEST);
        $this->response->redirect('work_progress/index/'.$_REQUEST["ticket_id"]);
    }
    
}