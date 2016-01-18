<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');
App::uses('SaleModel', 'Model');
App::uses('CakeTime', 'Utility');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class SalesController extends AppController {
//	public $components = array('PhpExcel.PhpExcel');
	public $helpers = array('Html', 'Form', 'Time');


	/* executes sql query and sets columns and datafields for the view  */
	private function fetch_sales($range) {
/*

	This works, but I rather created and view, that includes both tables, like this:

	create view sales as select '1',sale_id, customer_id, sale_amount, sale_date from sales1 union select '2', sale_id, customer_id, sale_amount, sale_date from sales2;

		$sql=<<<ESQL
		select
			c.customer_id,
			c.firstname,
			c.lastname,
			max(s.sale_date) as sale_date,
			count(1) as sales_count,
			sum(sale_amount) as sales_sum
		from customer as c, (select * from sales1 UNION select * from sales2) as s
		where c.customer_id = s.customer_id
		group by c.customer_id
		ESQL;
		$sales = $this->Sale->query($sql);
*/
		$from = $range['from'].' 00:00:00';
		$till = $range['till'].' 23:59:59';
		$sales = $this->Sale->find('all',
			array(
				'group' => 'Customer.customer_id',
				'fields' => array(
					'Customer.customer_id',
					'Customer.firstname',
					'Customer.lastname',
					'max(Sale.sale_date) as sale_date',
					'count(1) as sales_count',
					'sum(Sale.sale_amount) as sales_sum'
				),
				'conditions' => array(
					'Sale.sale_date >=' => $from,
					'Sale.sale_date <=' => $till
				)
			));
		$this->set('sales', $sales);
	}
	/* if the user provided a range, we override the parameters from the 
	 * path. This is all quite hacky, as I don't quite understand CakePHP yet. */
	private function get_range_by_param_or_request($from, $till) {
		$range = array();
		if (!empty($this->request->data['Sale'])) {
			$from = $this->request->data['Sale']['from'];
			$till = $this->request->data['Sale']['till'];
			$from = CakeTime::format('Y-m-d', implode('-', $from));
			$till = CakeTime::format('Y-m-d', implode('-', $till));
		}
		$this->set('from', $from);
		$this->set('till', $till);
		/* probably not ok. Why is data not private?  */
		$this->request->data['Sale']['from'] = $from;
		$this->request->data['Sale']['till'] = $till;
		return array('from'=>$from, 'till'=>$till);
	}
	public function index($from, $till) {
		$range = $this->get_range_by_param_or_request($from, $till);
		$this->fetch_sales($range);
		$this->set('columns', array(
			'Customer name',
			'Sales count',
			'Sales sum',
			'Sales date'
		));
	}
	public function export($from, $till) {
		$range = $this->get_range_by_param_or_request($from, $till);
		$this->fetch_sales($range);
		$filename = "export-".$range['from']."-".$range['till'].".csv";
		$this->response->download($filename);
		$this->layout = 'ajax';
		$this->set('columns', array(
			'customerid',
			'firstname',
			'lastname',
			'sales_date',
			'sales_count',
			'sales_sum',
		));
	}
}
