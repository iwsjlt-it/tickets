<?php
use Phalcon\Mvc\Controller;

/**
 * Хз
 */
class ObnovaController extends Controller
{

	public function indexAction()
	{
		$this->view->pageTitle = 'Obnova';
	}
}