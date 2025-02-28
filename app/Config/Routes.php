<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true); // Enables automatic routing

/**
 * --------------------------------------------------------------------
 * Custom Defined Routes
 * --------------------------------------------------------------------
 */

// Homepage
$routes->get('/', 'Home::index');

// Game Lobby
$routes->get('/game_lobby', 'GameLobby::index');
$routes->post('/create_lobby', 'GameLobby::createLobby');
$routes->post('/join_lobby', 'GameLobby::joinLobby');
$routes->get('/game_lobby/(:any)', 'GameLobby::showLobby/$1');
$routes->get('/get_lobby_players/(:any)', 'GameLobby::getLobbyPlayers/$1');
$routes->get('/check_host_status/(:any)', 'GameLobby::checkHostStatus/$1');
$routes->get('/close_lobby/(:any)', 'GameLobby::closeLobby/$1');
$routes->get('/leave_lobby/(:any)', 'GameLobby::leaveLobby/$1');


// Leaderboard Page
$routes->get('/leaderboard', 'Leaderboard::index');

// Authentication Routes
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::attemptLogin');

$routes->get('/logout', 'Auth::logout');

$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::attemptRegister');

