<?php

return [
    'create' => [
        'single' => [
            'label' => 'Criar',
            'modal' => [
                'heading' => 'Criar :label',
                'actions' => [
                    'create' => [
                        'label' => 'Criar',
                    ],
                    'create_another' => [
                        'label' => 'Criar e adicionar outro',
                    ],
                ],
            ],
            'notifications' => [
                'created' => [
                    'title' => 'Criado',
                ],
            ],
        ],
    ],

    'edit' => [
        'single' => [
            'label' => 'Editar',
            'modal' => [
                'heading' => 'Editar :label',
                'actions' => [
                    'save' => [
                        'label' => 'Salvar alterações',
                    ],
                ],
            ],
            'notifications' => [
                'saved' => [
                    'title' => 'Salvo',
                ],
            ],
        ],
    ],

    'delete' => [
        'single' => [
            'label' => 'Excluir',
            'modal' => [
                'heading' => 'Excluir :label',
                'description' => 'Tem certeza de que deseja excluir este registro? Esta ação não pode ser desfeita.',
                'actions' => [
                    'delete' => [
                        'label' => 'Excluir',
                    ],
                ],
            ],
            'notifications' => [
                'deleted' => [
                    'title' => 'Excluído',
                ],
            ],
        ],
        'multiple' => [
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
    ],

    'force_delete' => [
        'single' => [
            'label' => 'Excluir permanentemente',
            'modal' => [
                'heading' => 'Excluir :label permanentemente',
                'description' => 'Tem certeza de que deseja excluir permanentemente este registro? Esta ação não pode ser desfeita.',
                'actions' => [
                    'delete' => [
                        'label' => 'Excluir',
                    ],
                ],
            ],
            'notifications' => [
                'deleted' => [
                    'title' => 'Excluído',
                ],
            ],
        ],
        'multiple' => [
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
    ],

    'restore' => [
        'single' => [
            'label' => 'Restaurar',
            'modal' => [
                'heading' => 'Restaurar :label',
                'description' => 'Tem certeza de que deseja restaurar este registro?',
                'actions' => [
                    'restore' => [
                        'label' => 'Restaurar',
                    ],
                ],
            ],
            'notifications' => [
                'restored' => [
                    'title' => 'Restaurado',
                ],
            ],
        ],
        'multiple' => [
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

    'replicate' => [
        'single' => [
            'label' => 'Duplicar',
            'modal' => [
                'heading' => 'Duplicar :label',
                'actions' => [
                    'replicate' => [
                        'label' => 'Duplicar',
                    ],
                ],
            ],
            'notifications' => [
                'replicated' => [
                    'title' => 'Duplicado',
                ],
            ],
        ],
    ],

    'cancel' => [
        'label' => 'Cancelar',
    ],

    'save' => [
        'label' => 'Salvar',
    ],

    'submit' => [
        'label' => 'Enviar',
    ],
];