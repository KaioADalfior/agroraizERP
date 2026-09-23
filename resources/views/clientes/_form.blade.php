@csrf

<div
    class="grid gap-6 sm:grid-cols-2"
    x-data="{
        cep: @js(old('cep', $cliente->cep)),
        endereco: @js(old('endereco', $cliente->endereco)),
        numero: @js(old('numero', $cliente->numero)),
        complemento: @js(old('complemento', $cliente->complemento)),
        bairro: @js(old('bairro', $cliente->bairro)),
        cidade: @js(old('cidade', $cliente->cidade)),
        estado: @js(old('estado', $cliente->estado)),
        buscandoCep: false,
        erroCep: false,
        formatarCep() {
            let numeros = this.cep.replace(/\D/g, '').slice(0, 8);
            if (numeros.length > 5) {
                numeros = numeros.slice(0, 5) + '-' + numeros.slice(5);
            }
            this.cep = numeros;
        },
        async buscarCep() {
            const cepLimpo = this.cep.replace(/\D/g, '');
            if (cepLimpo.length !== 8) {
                return;
            }

            this.buscandoCep = true;
            this.erroCep = false;

            try {
                const resposta = await fetch(`https://viacep.com.br/ws/${cepLimpo}/json/`);
                const dados = await resposta.json();

                if (dados.erro) {
                    this.erroCep = true;
                } else {
                    this.endereco = dados.logradouro || this.endereco;
                    this.bairro = dados.bairro || this.bairro;
                    this.cidade = dados.localidade || this.cidade;
                    this.estado = dados.uf || this.estado;
                }
            } catch (erro) {
                this.erroCep = true;
            } finally {
                this.buscandoCep = false;
            }
        },
    }"
>
    <div class="sm:col-span-2">
        <x-input
            name="nome"
            label="Cliente / Proprietário"
            :value="old('nome', $cliente->nome)"
            placeholder="Nome do cliente ou proprietário"
            autofocus
            required
        />
    </div>

    <p class="text-sm font-semibold uppercase tracking-[0.1em] text-raiz-600 sm:col-span-2">Endereço</p>

    <div>
        <x-input
            name="cep"
            label="CEP"
            x-model="cep"
            x-on:input="formatarCep()"
            x-on:blur="buscarCep()"
            placeholder="00000-000"
            inputmode="numeric"
            maxlength="9"
            required
        />
        <p class="mt-1.5 text-sm text-raiz-500" x-show="buscandoCep" x-cloak>Buscando endereço…</p>
        <p class="mt-1.5 text-sm text-red-600" x-show="erroCep" x-cloak>CEP não encontrado. Preencha o endereço manualmente.</p>
    </div>

    <x-input name="numero" label="Número" x-model="numero" placeholder="Ex.: 123" />

    <div class="sm:col-span-2">
        <x-input name="endereco" label="Rua" x-model="endereco" placeholder="Rua, avenida..." required />
    </div>

    <x-input name="complemento" label="Complemento (opcional)" x-model="complemento" placeholder="Apto, bloco, referência..." />

    <x-input name="bairro" label="Bairro" x-model="bairro" required />

    <x-input name="cidade" label="Cidade" x-model="cidade" required />

    <x-select name="estado" label="Estado" x-model="estado" required>
        <option value="">Selecione</option>
        @foreach ($estados as $sigla => $nomeEstado)
            <option value="{{ $sigla }}">{{ $sigla }} - {{ $nomeEstado }}</option>
        @endforeach
    </x-select>

    <x-input
        name="data"
        type="date"
        label="Data"
        :value="old('data', optional($cliente->data)->format('Y-m-d'))"
    />

    <div class="sm:col-span-2">
        <x-textarea
            name="observacao"
            label="Observação"
            :value="old('observacao', $cliente->observacao)"
            rows="4"
            placeholder="Anotações sobre este cliente"
        />
    </div>
</div>
