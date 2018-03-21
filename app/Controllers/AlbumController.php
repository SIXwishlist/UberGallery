<?php

namespace App\Controllers;

use App\Album;
use App\Exceptions\FileNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class AlbumController extends Controller
{
    /**
     * Handle an incoming Album request and return a response.
     *
     * @param \Slim\Http\Request  $request  Incoming request object
     * @param \Slim\Http\Response $response Outgoing response object
     * @param array               $args     the array of request arguments
     *
     * @return \Slim\Http\Response
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        try {
            $album = new Album($args['album'], $this->container->config->split("albums.{$args['album']}"));
        } catch (FileNotFoundException $exception) {
            return $response->withStatus(404)->write('Album not found');
        }

        $album->sort(
            $this->config("albums.{$args['album']}.sort.method", 'name'),
            $this->config("albums.{$args['album']}.sort.reverse", false)
        );

        return $response->write($this->view('album', [
            'slug' => $album->slug(),
            'title' => $album->title(),
            'images' => $album->images($args['page'] ?? 1)->all()
        ]));
    }
}
