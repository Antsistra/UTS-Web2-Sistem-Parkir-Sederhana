<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/parkir', 'Parkir::index');
$routes->get('/parkir/create', 'Parkir::create');
$routes->post('/parkir/store', 'Parkir::store');
$routes->get('/parkir/checkout/(:num)', 'Parkir::checkout/$1');
$routes->get('/parkir/income', 'Parkir::income');
$routes->get('/parkir/history', 'Parkir::history');
