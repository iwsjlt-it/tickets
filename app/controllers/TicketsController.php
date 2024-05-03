<?php
use Phalcon\Mvc\Controller;
use Phalcon\Paginator\Adapter\Model as ModelPaginator;
/**
 * Тикеты
 */
class TicketsController extends Controller
{

	public function indexAction()
	{
		$this->view->pageTitle = 'Тикеты';
	}
	
	public function testAction()
	{
	    $this->view->pick('tickets/test/test');
		$this->view->pageTitle = 'Тест';
		$conditionBuilder = new ConditionBuilder($_GET);
		
        $currentPage = (int) ($_GET["page"]??1);	 // GET запрос
        $paginator = new ModelPaginator(
       [
            "model" => Tickets::class,
            
            "parameters" => [
               'conditions' => $conditionBuilder->getConditions(),
               'bind'       => $conditionBuilder->getBind(),
               "order" => "name"
            ],
            
            "limit" => 15,
            "page"  => $currentPage,
        ]
        );
        $paginationResult = $paginator->paginate();
        
    	$this->view->paginationResult = $paginationResult;
	}
	
	public function creationAction()
	{
		$this->view->pageTitle = 'Создать тикет';
		
	}
	
	public function templatesAction()
	{
		$this->view->pageTitle = 'Шаблоны тикетов';
		
	}
	
	public function viewlistModalAction()
	{
		$this->view->pageTitle = 'Шаблоны тикетов (Модал)';
		
	}
	
	//отображает страницу редактирования тикета по параметру $id
	public function editticketAction($id)
	{
		$this->view->pageTitle = 'Изменить тикет';
		$this->view->item = Tickets::findFirstById( $id );
	}
	
	//обновляет данные о тикете
	public function updateAction($id)
	{
		$processor= new TicketsProcessor();
        $processor->update($id ,$_REQUEST);
        $this->response->redirect('tickets/viewlist');
		
	}


	public function viewlistAction()
	{
		$this->view->pageTitle = 'Список тикетов';
		
		$conditionBuilder = new ConditionBuilder($_GET);
	
        $currentPage = (int) ($_GET["page"]??1);	 // GET запрос
        $paginator = new ModelPaginator(
        [
            "model" => Tickets::class,
            
            "parameters" => [
                'conditions' => $conditionBuilder->getConditions(),
                'bind'       => $conditionBuilder->getBind(),
                "order" => "name"
            ],
            
            "limit" => 15,
            "page"  => $currentPage,
        ]
        );
        $paginationResult = $paginator->paginate();
        
    	$this->view->paginationResult = $paginationResult;
    	
    	$this->view->firms = Firm::find();
	}
	
	 public function sendAction() //отправка данных из формы
    {
        $processor= new TicketsProcessor();
        $processor->send($_REQUEST);
        $this->response->redirect('tickets/viewlist');
    }


} // End of class