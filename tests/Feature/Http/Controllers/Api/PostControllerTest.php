<?php

namespace Tests\Feature\Http\Controllers\Api;

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
        $this->withoutExceptionHandling();//nos presenta los errores de una manera mas manejable
        /**
         * Comprobamos que se estan guardando los datos en por medio de post mediante el api
         * para ello lo hacemos mediane json para que calquier aplicacion es conecte,
         * asiganmos el metodo,la ruta, y la informacion que deseamos 
         */
        $response = $this->json('POST','/api/post',[
            'title'=> 'The title',

        ]);
            /**
             * Cuando el paso anterior suceda, debemos asegurar que se esta retornando una estructura planificada con json 
             * y cuando se guarde retornamos los campos que asignemos. 
             */
        $response->assertJsonStructure(['id','title','created_at','updated_at'])
        /**
         * Una segunda comprobacion en donde aseguremos que 
         * la informacion que mandamos es la que se guardo
         */
        ->assertJson(['title'=>'The title'])
        /**
         * Aseguramos que la peticion http se completo y se ha creado un recurso en la base de datos
         */
        ->assertStatus(201);
        
        /**
         * Por ultimo verificamos que los datos existen en la base de datos 
         */
        $this->assertDatabaseHas('posts',['title'=>'The title']);
        
    }
}
