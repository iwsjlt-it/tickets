<?php
use Phalcon\Mvc\Controller;

/**
 * Инфо
 */
class InfoController extends Controller
{

	public function indexAction()
	{
		$this->view->pageTitle = 'Справочники';
	}
	
	public function editlistAction()
	{
		$this->view->pageTitle = 'Редактирование справочной информации';
		
	}
	
	public function viewlistAction()
	{
		$this->view->pageTitle = 'Справочная информация';
	}
	
		public function aboutlistAction()
	{
		$this->view->pageTitle = 'Техническое задание';
	}
	
		public function formlistAction()
	{
		$this->view->pageTitle = 'Добавление информации';
	}
	
	 public function sendAction() //отправка данных из формы
    {
       print_r($_POST);
       $newInfo = new Info();
        $newInfo->name = $_REQUEST['name'];
        $newInfo->link = $_REQUEST['link'];
        $newInfo->imglink = $_REQUEST['imglink'];
        $result= $newInfo->save();
        
        if (false === $result) {

            echo 'Error saving Invoice: ';

            $messages = $newInfo->getMessages();

            foreach ($messages as $message) 
            {
                echo $message . PHP_EOL;
            }
        } 
        else 
        {
            echo 'Record Saved';
        }
        
    }
	
} // End of class