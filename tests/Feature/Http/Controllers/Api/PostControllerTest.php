<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    /**
     * Empleamos la clase RefreshDatabase para construir los archivos de prueba necesarios, 
     * 1) Por lo que debemos configurar las migraciones  
     * 2) Configurar el modelo para que guarde los campos  
     *  */
    use RefreshDatabase;
    public function test_store()
    {
        $this->withoutExceptionHandling(); //nos presenta los errores de una manera mas manejable
        /**
         * Comprobamos que se estan guardando los datos en por medio de post mediante el api
         * para ello lo hacemos mediane json para que calquier aplicacion es conecte,
         * asiganmos el metodo,la ruta, y la informacion que deseamos 
         */
        $response = $this->json('POST', '/api/post', [
            'title' => 'The title',

        ]);
        /**
         * Cuando el paso anterior suceda, debemos asegurar que se esta retornando una estructura planificada con json 
         * y cuando se guarde retornamos los campos que asignemos. 
         */
        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            /**
         * Una segunda comprobacion en donde aseguremos que 
         * la informacion que mandamos es la que se guardo
         */
            ->assertJson(['title' => 'The title'])
            /**
         * Aseguramos que la peticion http se completo y se ha creado un recurso en la base de datos
         */
            ->assertStatus(201);

        /**
         * Por ultimo verificamos que los datos existen en la base de datos 
         */
        $this->assertDatabaseHas('posts', ['title' => 'The title']);
    }
    public function test_validate_title()
    {
        $response = $this->json('POST', '/api/post', [
            'title' => ''
        ]);
        //la solicitud esta bien hecha pero fue imposible completarla, ya que estmos validando que no posea titulos
        $response->assertStatus(422)
            ->assertJsonValidationErrors('title');
    }
    public function test_show()
    {
        /**
         * Creamoy gardamos un nuevo post en la base de datos, ademas lo guardamos en una variable para verificar que existe 
         * los buscamos por medio del id en la ruta y por utlimo completamos el asessert comprobando que el titulo que guardamos pertenece al titulo que se creo
         */
        $post = factory(Post::class)->create();
        $response = $this->json('GET', "/api/post/$post->id");
        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            ->assertJson(['title' => $post->title])
            ->assertStatus(200);
    }
    public function test_404_show()
    {
        $response = $this->json('GET', '/api/post/1000');
        $response->assertStatus(404);
    }
    public function test_update()
    {
        $this->withoutExceptionHandling();
        $post = factory(Post::class)->create();
        
        $response = $this->json('PUT', "/api/post/$post->id", [
            'title' => 'New title',

        ]);
        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            ->assertJson(['title' => 'New title'])
            ->assertStatus(200);
        $this->assertDatabaseHas('posts', ['title' => 'New title']);
    }
}
