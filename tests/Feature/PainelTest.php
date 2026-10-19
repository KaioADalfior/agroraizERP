<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PainelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_visitante_e_redirecionado_para_login(): void
    {
        $this->get('/painel')->assertRedirect('/login');
    }

    public function test_pagina_renderiza_para_usuario_autenticado(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/painel')
            ->assertOk()
            ->assertSee('Painel analítico')
            ->assertSee('Acessos rápidos');
    }

    public function test_contador_de_clientes_reflete_o_banco_real(): void
    {
        Cliente::factory()->count(3)->create();

        $resposta = $this->actingAs(User::factory()->create())->get('/painel');

        $resposta->assertOk();
        // 3 clientes criados pelo teste + nenhum outro: o valor exibido tem
        // que ser exatamente a contagem real da tabela `clientes`, nunca um
        // número fixo/fictício.
        $this->assertSame(3, Cliente::count());
        $resposta->assertSeeInOrder(['Clientes cadastrados', '3']);
    }

    public function test_contador_de_clientes_com_zero_clientes(): void
    {
        $resposta = $this->actingAs(User::factory()->create())->get('/painel');

        $resposta->assertOk()->assertSeeInOrder(['Clientes cadastrados', '0']);
    }

    public function test_grafico_de_cadastros_por_mes_soma_clientes_reais_do_mes_atual(): void
    {
        Cliente::factory()->count(2)->create(['created_at' => now()]);
        // Fora da janela dos últimos 6 meses: não deve contar.
        Cliente::factory()->create(['created_at' => now()->subMonths(8)]);

        $resposta = $this->actingAs(User::factory()->create())->get('/painel');

        $resposta->assertOk();

        $rotuloMesAtual = Str::ucfirst(now()->translatedFormat('M/y'));
        $resposta->assertSeeInOrder([$rotuloMesAtual, '2']);
    }

    public function test_grafico_de_cadastros_mostra_estado_vazio_sem_clientes_no_periodo(): void
    {
        // Sem nenhum cliente, o componente de gráfico deve mostrar a
        // mensagem de estado vazio, nunca inventar valores.
        $resposta = $this->actingAs(User::factory()->create())->get('/painel');

        $resposta->assertOk()->assertSee('Sem dados ainda.');
    }

    public function test_grafico_custeio_x_investimento_usa_contagem_real(): void
    {
        $cliente = Cliente::factory()->create();
        $cliente->procedimentos()->create(['nome' => 'A', 'tipo_operacao' => 'Custeio']);
        $cliente->procedimentos()->create(['nome' => 'B', 'tipo_operacao' => 'Custeio']);
        $cliente->procedimentos()->create(['nome' => 'C', 'tipo_operacao' => 'Investimento']);

        $resposta = $this->actingAs(User::factory()->create())->get('/painel');

        $resposta->assertOk()
            ->assertSee('Custeio (2)')
            ->assertSee('Investimento (1)');
    }

    public function test_grafico_custeio_x_investimento_mostra_estado_vazio_sem_procedimentos(): void
    {
        $resposta = $this->actingAs(User::factory()->create())->get('/painel');

        $resposta->assertOk()->assertSee('Nenhum procedimento cadastrado ainda.');
    }

    public function test_grafico_de_fases_mostra_as_fases_reais_mais_frequentes_em_ordem(): void
    {
        $cliente = Cliente::factory()->create();
        $cliente->procedimentos()->create(['nome' => 'A', 'tipo_operacao' => 'Custeio', 'fase_proposta' => 'Aprovado']);
        $cliente->procedimentos()->create(['nome' => 'B', 'tipo_operacao' => 'Custeio', 'fase_proposta' => 'Aprovado']);
        $cliente->procedimentos()->create(['nome' => 'C', 'tipo_operacao' => 'Investimento', 'fase_proposta' => 'Em análise']);
        // fase_proposta vazia/nula não deve aparecer no gráfico.
        $cliente->procedimentos()->create(['nome' => 'D', 'tipo_operacao' => 'Custeio', 'fase_proposta' => null]);

        $resposta = $this->actingAs(User::factory()->create())->get('/painel');

        $resposta->assertOk()->assertSeeInOrder(['Aprovado', '2', 'Em análise', '1']);
    }

    public function test_grafico_de_fases_mostra_estado_vazio_sem_fases_registradas(): void
    {
        $resposta = $this->actingAs(User::factory()->create())->get('/painel');

        $resposta->assertOk()->assertSee('Nenhum procedimento com fase registrada ainda.');
    }

    public function test_pagina_nao_vaza_o_javascript_do_bloco_de_saudacao_como_texto_visivel(): void
    {
        // Mesma classe de regressão já corrigida na aba de Procedimentos:
        // garante que o novo x-data da saudação/relógio (em painel.blade.php)
        // também não tem nenhuma aspa dupla literal escondida que quebraria
        // o atributo e vazaria o JavaScript como texto da página.
        $html = $this->actingAs(User::factory()->create())
            ->get('/painel')
            ->assertOk()
            ->getContent();

        $dom = new \DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>'.$html);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        $textoVisivel = '';
        foreach ($xpath->query('//body//text()[not(ancestor::script)]') as $node) {
            $textoVisivel .= $node->nodeValue;
        }

        $this->assertStringNotContainsString('clearInterval', $textoVisivel);
        $this->assertStringNotContainsString('minutosDoDiaEmBrasilia', $textoVisivel);
        $this->assertStringNotContainsString('Intl.DateTimeFormat', $textoVisivel);
        $this->assertStringNotContainsString('relogioBrasilia', $textoVisivel);
    }

    public function test_saudacao_exibe_o_primeiro_nome_do_usuario_autenticado(): void
    {
        $usuario = User::factory()->create(['name' => 'Maria da Silva']);

        $html = $this->actingAs($usuario)->get('/painel')->assertOk()->getContent();

        // O primeiro nome vem embutido via @js() dentro do x-data; o teste
        // confirma que o valor real do usuário logado foi embutido (não um
        // nome fixo), já que o texto "Maria" só aparece renderizado pelo
        // Alpine no navegador — aqui conferimos que o dado chega correto ao
        // HTML enviado.
        $this->assertStringContainsString("nome: 'Maria'", $html);
    }

    public function test_saudacao_sem_nome_nao_quebra_quando_usuario_nao_tem_nome_cadastrado(): void
    {
        $usuario = User::factory()->create(['name' => '']);

        $html = $this->actingAs($usuario)->get('/painel')->assertOk()->getContent();

        $this->assertStringContainsString('nome: null', $html);
    }

    public function test_acessos_rapidos_so_usam_rotas_que_existem_de_fato(): void
    {
        // "Banco de dados" aponta para a rota real culturas.index; os demais
        // itens marcados "Em breve" não têm href algum (não inventam rota).
        $resposta = $this->actingAs(User::factory()->create())->get('/painel');

        $resposta->assertOk()
            ->assertSee(route('culturas.index'), false)
            ->assertSee('Em breve');
    }

    public function test_acesso_rapido_clientes_abre_o_mesmo_modal_do_menu_lateral(): void
    {
        // Reaproveita o x-data do layout (modalClientesAberto) em vez de
        // inventar um modal novo ou uma rota nova para "Clientes".
        $resposta = $this->actingAs(User::factory()->create())->get('/painel');

        $resposta->assertOk()->assertSee('modalClientesAberto = true', false);
    }

    public function test_rota_banco_de_dados_referenciada_no_acesso_rapido_responde_ok(): void
    {
        $usuario = User::factory()->create();

        $this->actingAs($usuario)->get('/painel')->assertOk();
        $this->actingAs($usuario)->get(route('culturas.index'))->assertOk();
    }
}
