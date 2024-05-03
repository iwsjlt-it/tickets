<?php
use Phalcon\Mvc\Controller;
/**
 * Линии
 */
class LineController extends Controller
{
	public function listAction()
	{
		$this->view->pageTitle = 'Список линий связи';
		$this->view->items = Line::find();
	}
	
	public function editAction($id)
	{
		$this->view->pageTitle = 'Редактирование линии связи';
		$this->view->item = Line::findFirstById($id);
	}
	
	public function catchAction($id)
	{
		if("new" == $id)
		{
		$line = new Line();
        $line->title = $_REQUEST['title'];
        $this->_saveLine( $line );
        $this->response->redirect("/line/list");
        
		}
		else
		{
		$line = Line::findFirstById( $id );
        $line->title = $_REQUEST['title'];
        $line->save();
        $this->response->redirect("/line/list");
		}
	}
	
	protected function _saveLine( Line $line )
    {
        if ( !$line->save() ) {
            $messages = [];
            foreach ( $line->getMessages() as $message )
                $messages[] = $message;
            throw new RuntimeException( 'Ошибка сохранения: '.join("\n",$messages) );
        }
    }
}