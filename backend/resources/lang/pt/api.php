<?php

return [
    'created' => ':name criado(a) com sucesso.',
    'updated' => ':name atualizado(a) com sucesso.',
    'deleted' => ':name excluído(a) com sucesso.',
    'not_found' => ':name não encontrado(a).',
    'validation_error' => 'Erro de validação.',
    'unauthorized' => 'Não autorizado.',
    'forbidden' => 'Acesso negado.',
    'server_error' => 'Erro interno do servidor.',
    'success' => 'Operação realizada com sucesso.',
    'failed' => 'Operação falhou.',
    'reservation_conflict' => 'Existe uma reserva realizada para esse momento.',
    'table_unavailable' => '',
    'insufficient_table_capacity' => ''
];


        if ($reservation->hasConflict($excludeId)) {
            throw new ApiException(__('api.reservation_conflict'));
        }

        if (!$reservation->tableIsAvailable()) {
            throw new ApiException(__('api.table_unavailable'));
        }

        if (!$reservation->tableHasCapacity()) {
            throw new ApiException(__('api.insufficient_table_capacity'));
        }
