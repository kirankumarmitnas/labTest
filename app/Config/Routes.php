<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
//$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Admin\Account::login');


$routes->group('admin',["filter" => "authGuard"], function ($routes) {
	$routes->get('login', 'Admin\Account::login');
	$routes->get('logout', 'Admin\Account::logout');
	$routes->post('login', 'Admin\Account::userValidate');
	//$routes->post('user/login/historys', function () { dd($this->request->getGetPost()); });
	$routes->get('user/login/historys','Admin\Notification::loginLog');
	$routes->get('db/backup/download','Admin\Account::backupDownload');
	$routes->get('dashboard', 'Admin\Account');
	$routes->group('notification', function ($routes) {
		$routes->get('email/list','Admin\Notification::emailList');
		$routes->get('sms/list','Admin\Notification::smsList');
	});
	$routes->group('service', function ($routes) {
		$routes->get('list','Admin\Service::listDetails');
		$routes->post('add','Admin\Service::add');
		$routes->post('update','Admin\Service::update');
		$routes->post('remove','Admin\Service::remove');
		$routes->group('category', function ($routes) {
			$routes->get('list','Admin\Category::listDetails');
			$routes->post('add','Admin\Category::add');
			$routes->post('update','Admin\Category::update');
			$routes->post('remove','Admin\Category::remove');
			$routes->post('status','Admin\Category::status');
			$routes->get('list/details','Admin\Category::listInfo');
		});
	});
	$routes->group('test', function ($routes) {
		$routes->get('list','Admin\Test::listDetails');
		$routes->post('details','Admin\ManageTest::details');
		$routes->get('add','Admin\ManageTest::addView/$1');
		$routes->get('update/(:num)','Admin\ManageTest::updateView/$1');
		$routes->post('add','Admin\ManageTest::addData');
		$routes->post('update/(:num)','Admin\ManageTest::updateData/$1');
		$routes->post('remove','Admin\ManageTest::remove');
	});
	$routes->group('report', function ($routes) {
		$routes->get('doctor','Admin\Report::doctorDetails');
		$routes->get('monthly','Admin\Report::monthlyDetails');
		$routes->get('service','Admin\Report::serviceDetails');
		$routes->get('export','Admin\Report::exportFile');
	});
	$routes->group('payment', function ($routes) {
		$routes->get('list','Admin\Payment::listDetails');
		$routes->get('list/export','Admin\Payment::exportFile');
	});
	$routes->group('doctor', function ($routes) {
		$routes->get('list','Admin\Doctor::listDetails');
		$routes->post('add','Admin\Doctor::add');
		$routes->post('update','Admin\Doctor::update');
		$routes->post('remove','Admin\Doctor::remove');
		$routes->post('status','Admin\Doctor::status');
	});
});

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
