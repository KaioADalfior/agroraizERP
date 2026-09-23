<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_a_raiz_do_site_redireciona_para_o_painel(): void
    {
        $this->get('/')->assertRedirect('/painel');
    }
}
