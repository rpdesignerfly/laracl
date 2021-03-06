<?php
/**
 * @see       https://github.com/rpdesignerfly/access-control
 * @copyright Copyright (c) 2018 Ricardo Pereira Dias (https://rpdesignerfly.github.io)
 * @license   https://github.com/rpdesignerfly/access-control/blob/master/license.md
 */

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Acl\Tests\Libs\IControllerTestCase;
use Acl\Models;

class GroupsControllerTest extends IControllerTestCase
{
    use RefreshDatabase;

    // Para verificar as rotas disponiveis
    // php artisan route:list

    public function testIndex()
    {
        $user = \App\User::find(1);
        $this->actingAs($user);

        $response = $this->get('/acl/groups');
        $response->assertStatus(200);
    }

    // CREATE
    public function testCreate()
    {
        $user = \App\User::find(1);
        $this->actingAs($user);

        $response = $this->get('/acl/groups/create');
        $response->assertStatus(200);
    }

    public function testStore()
    {
        $user = \App\User::find(1);
        $this->actingAs($user);

        // Dados enviados por POST
        $faker = \Faker\Factory::create();
        $name = $faker->name;
        $post = [
            'name' => $name,
        ];

        // Requisição POST
        $response = $this->post('/acl/groups', $post);

        // Usuário criado
        $group = Models\AclGroup::where('name', $name)->first();
        $response->assertStatus(302);
        $response->assertRedirect("/acl/groups");
    }

    public function testEdit()
    {
        $group = self::createGroup();

        $user = \App\User::find(1);
        $this->actingAs($user);

        $response = $this->get('/acl/groups/' . $group->id . '/edit');
        $response->assertStatus(200);
    }

    public function testUpdate()
    {
        $user = \App\User::find(1);
        $this->actingAs($user);

        // Dados enviados por PUT
        $faker = \Faker\Factory::create();
        $name = $faker->name;
        $put = [
            'name' => $name,
        ];

        // Requisição PUT
        $original_group = self::createGroup();
        $response = $this->put("/acl/groups/" . $original_group->id, $put, [
            'HTTP_REFERER' => "/acl/groups/" . $original_group->id . "/edit"
        ]);

        $response->assertStatus(302);
        $response->assertRedirect("/acl/groups/" . $original_group->id . "/edit");

        // Grupo atualizado
        $edited_group = Models\AclGroup::find($original_group->id);
        $this->assertNotEquals($original_group->name, $edited_group->name);
    }
}
