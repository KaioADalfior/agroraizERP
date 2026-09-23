@csrf

<div class="grid gap-6 sm:grid-cols-2">
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

    <x-input
        name="cidade_estado"
        label="Cidade/Estado"
        :value="old('cidade_estado', $cliente->cidade_estado)"
        placeholder="Ex.: Curaçá-BA"
    />

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
