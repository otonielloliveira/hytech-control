<?php

return [
    'actions' => [
        'attach' => [
            'single' => [
                'label' => 'Anexar',
                'modal' => [
                    'heading' => 'Anexar :label',
                    'fields' => [
                        'record_id' => [
                            'label' => 'Registro',
                        ],
                    ],
                    'actions' => [
                        'attach' => [
                            'label' => 'Anexar',
                        ],
                        'attach_another' => [
                            'label' => 'Anexar e anexar outro',
                        ],
                    ],
                ],
                'notifications' => [
                    'attached' => [
                        'title' => 'Anexado',
                    ],
                ],
            ],
        ],

        'detach' => [
            'single' => [
                'label' => 'Desanexar',
                'modal' => [
                    'heading' => 'Desanexar :label',
                    'actions' => [
                        'detach' => [
                            'label' => 'Desanexar',
                        ],
                    ],
                ],
                'notifications' => [
                    'detached' => [
                        'title' => 'Desanexado',
                    ],
                ],
            ],
            'multiple' => [
                'label' => 'Desanexar selecionados',
                'modal' => [
                    'heading' => 'Desanexar :label selecionados',
                    'actions' => [
                        'detach' => [
                            'label' => 'Desanexar',
                        ],
                    ],
                ],
                'notifications' => [
                    'detached' => [
                        'title' => 'Desanexados',
                    ],
                ],
            ],
        ],

        'dissociate' => [
            'single' => [
                'label' => 'Dissociar',
                'modal' => [
                    'heading' => 'Dissociar :label',
                    'actions' => [
                        'dissociate' => [
                            'label' => 'Dissociar',
                        ],
                    ],
                ],
                'notifications' => [
                    'dissociated' => [
                        'title' => 'Dissociado',
                    ],
                ],
            ],
            'multiple' => [
                'label' => 'Dissociar selecionados',
                'modal' => [
                    'heading' => 'Dissociar :label selecionados',
                    'actions' => [
                        'dissociate' => [
                            'label' => 'Dissociar',
                        ],
                    ],
                ],
                'notifications' => [
                    'dissociated' => [
                        'title' => 'Dissociados',
                    ],
                ],
            ],
        ],

        'create' => [
            'label' => 'Nova :label',
        ],

        'edit' => [
            'label' => 'Editar',
        ],

        'view' => [
            'label' => 'Visualizar',
        ],

        'delete' => [
            'label' => 'Excluir',
        ],

        'force_delete' => [
            'label' => 'Excluir permanentemente',
        ],

        'restore' => [
            'label' => 'Restaurar',
        ],

        'replicate' => [
            'label' => 'Duplicar',
        ],
    ],

    'bulk_actions' => [
        'delete' => [
            'label' => 'Excluir selecionados',
            'modal' => [
                'heading' => 'Excluir :label selecionados',
                'description' => 'Tem certeza de que deseja excluir estes registros? Esta ação não pode ser desfeita.',
                'actions' => [
                    'delete' => [
                        'label' => 'Excluir',
                    ],
                ],
            ],
            'notifications' => [
                'deleted' => [
                    'title' => 'Excluídos',
                ],
            ],
        ],

        'force_delete' => [
            'label' => 'Excluir selecionados permanentemente',
            'modal' => [
                'heading' => 'Excluir :label selecionados permanentemente',
                'description' => 'Tem certeza de que deseja excluir permanentemente estes registros? Esta ação não pode ser desfeita.',
                'actions' => [
                    'delete' => [
                        'label' => 'Excluir',
                    ],
                ],
            ],
            'notifications' => [
                'deleted' => [
                    'title' => 'Excluídos',
                ],
            ],
        ],

        'restore' => [
            'label' => 'Restaurar selecionados',
            'modal' => [
                'heading' => 'Restaurar :label selecionados',
                'description' => 'Tem certeza de que deseja restaurar estes registros?',
                'actions' => [
                    'restore' => [
                        'label' => 'Restaurar',
                    ],
                ],
            ],
            'notifications' => [
                'restored' => [
                    'title' => 'Restaurados',
                ],
            ],
        ],
    ],

    'empty_state' => [
        'heading' => 'Nenhum registro encontrado',
        'description' => 'Crie um :label para começar.',
        'actions' => [
            'create' => [
                'label' => 'Nova :label',
            ],
        ],
    ],

    'filters' => [
        'actions' => [
            'remove' => [
                'label' => 'Remover filtro',
            ],
            'remove_all' => [
                'label' => 'Remover todos os filtros',
            ],
            'reset' => [
                'label' => 'Redefinir',
            ],
        ],

        'heading' => 'Filtros',

        'multi_select' => [
            'placeholder' => 'Todos',
        ],

        'select' => [
            'placeholder' => 'Todos',
        ],

        'trashed' => [
            'label' => 'Registros excluídos',
            'only_trashed' => 'Apenas excluídos',
            'with_trashed' => 'Com excluídos',
            'without_trashed' => 'Sem excluídos',
        ],
    ],

    'header_actions' => [
        'create' => [
            'label' => 'Nova :label',
        ],
    ],

    'pagination' => [
        'label' => 'Navegação de paginação',

        'overview' => '{1} resultado|[2,*] :first a :last de :total resultados',

        'fields' => [
            'records_per_page' => [
                'label' => 'por página',

                'options' => [
                    'all' => 'Todos',
                ],
            ],
        ],

        'actions' => [
            'go_to_page' => [
                'label' => 'Ir para a página :page',
            ],
            'next' => [
                'label' => 'Próximo',
            ],
            'previous' => [
                'label' => 'Anterior',
            ],
        ],
    ],

    'reorder' => [
        'single' => [
            'label' => 'Reordenar',
            'modal' => [
                'heading' => 'Reordenar :label',
                'actions' => [
                    'save' => [
                        'label' => 'Salvar ordem',
                    ],
                ],
            ],
            'notifications' => [
                'saved' => [
                    'title' => 'Ordem salva',
                ],
            ],
        ],
    ],

    'search' => [
        'label' => 'Pesquisar',
        'placeholder' => 'Pesquisar',
        'indicator' => 'Pesquisar',
    ],

    'selection' => [
        'select_all' => [
            'label' => 'Selecionar todos :count registros',
        ],
        'select_page' => [
            'label' => 'Selecionar todos na página',
        ],
        'deselect_all' => [
            'label' => 'Desmarcar todos',
        ],
    ],
];