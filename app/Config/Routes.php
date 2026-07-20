<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Default route - redirect to client login
$routes->get('/', 'Client::index');

// Client routes - grouped and secured
$routes->group('client', function($routes) {
    // Public routes (login/logout)
    $routes->get('login', 'Client::login');
    $routes->post('authenticate', 'Client::authenticate');
    $routes->get('logout', 'Client::logout');
    
    // Protected routes (require authentication)
    $routes->get('solde', 'Client::solde');
    $routes->get('depot', 'Client::depot');
    $routes->get('retrait', 'Client::retrait');
    $routes->get('transfert', 'Client::transfert');
    $routes->get('historique', 'Client::historique');
});

// Operateur routes - grouped
$routes->group('operateur', function($routes) {
    $routes->get('comptes', 'Operateur::comptes');
    $routes->get('gains', 'Operateur::gains');
    $routes->get('operations', 'Operateur::operations');
    $routes->get('prefixes', 'Operateur::prefixes');
});
