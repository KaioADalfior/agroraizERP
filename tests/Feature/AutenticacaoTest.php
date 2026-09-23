<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutenticacaoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Os testes não precisam do build do Vite/Tailwind.
        $this->withoutVite();
    }

    public function test_visitante_e_enviado_para_o_login(): void
    {
        $this->get('/painel')->assertRedirect('/login');
    }

    public function test_tela_de_login_e_exibida(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Entrar');
    }

    public function test_usuario_faz_login_com_credenciais_corretas(): void
    {
        $usuario = User::factory()->create(['password' => 'senha-segura-123']);

        $this->post('/login', [
            'email' => $usuario->email,
            'password' => 'senha-segura-123',
        ])->assertRedirect('/painel');

        $this->assertAuthenticatedAs($usuario);
    }

    public function test_login_falha_com_senha_incorreta(): void
    {
        $usuario = User::factory()->create(['password' => 'senha-segura-123']);

        $this->from('/login')->post('/login', [
            'email' => $usuario->email,
            'password' => 'senha-errada',
        ])->assertRedirect('/login')->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_e_bloqueado_apos_muitas_tentativas(): void
    {
        $usuario = User::factory()->create(['password' => 'senha-segura-123']);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => $usuario->email, 'password' => 'errada']);
        }

        $this->post('/login', [
            'email' => $usuario->email,
            'password' => 'senha-segura-123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_usuario_logado_ve_o_painel(): void
    {
        $usuario = User::factory()->create(['name' => 'Maria Souza']);

        $this->actingAs($usuario)
            ->get('/painel')
            ->assertOk()
            ->assertSee('Olá, Maria');
    }

    public function test_usuario_logado_nao_ve_a_tela_de_login(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/login')
            ->assertRedirect('/painel');
    }

    public function test_usuario_faz_logout(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/logout')
            ->assertRedirect('/login');

        $this->assertGuest();
    }
}
