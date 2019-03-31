<?php
namespace App\Controllers;
use Psr\Container\ContainerInterface;

class HomeController
{
  protected $container;

  // constructor receives container instance
  public function __construct(ContainerInterface $container) {
    $this->container = $container;
  }

  public function index($request, $response, $args) {

    return $this->container->view->render($response, 'home.twig', [
      'title' => 'Home'
    ]);
  }
}