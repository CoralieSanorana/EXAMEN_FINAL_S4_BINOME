<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('/client/login', 'Client::login');
$routes->get('/client/depot', 'Client::depot');
$routes->get('/client/historique', 'Client::historique');
$routes->get('/client/retrait', 'Client::retrait');
$routes->get('/client/solde', 'Client::solde');
$routes->get('/client/transfert', 'Client::transfert');

$routes->get('/operateur/login', 'Operateur::login');
$routes->post('/operateur/login', 'Operateur::authentificate');
$routes->get('/operateur/comptes', 'Operateur::comptes');
$routes->get('/operateur/gains', 'Operateur::gains');
$routes->get('/operateur/operations', 'Operateur::operations');
$routes->get('/operateur/prefixes', 'Operateur::prefixes');
