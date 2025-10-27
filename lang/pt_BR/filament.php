<?php

return [
    'actions' => [
        'create' => [
            'single' => [
                'label' => 'Criar',
                'modal' => [
                    'heading' => 'Nova :label',
                ],
            ],
            'multiple' => [
                'label' => 'Criar :label',
                'modal' => [
                    'heading' => 'Criar :label',
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
                            'label' => 'Salvar',
                        ],
                    ],
                ],
            ],
        ],
        'delete' => [
            'single' => [
                'label' => 'Excluir',
                'modal' => [
                    'heading' => 'Excluir :label',
                    'actions' => [
                        'delete' => [
                            'label' => 'Excluir',
                        ],
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
    ],
    
    'modal' => [
        'actions' => [
            'cancel' => [
                'label' => 'Cancelar',
            ],
            'confirm' => [
                'label' => 'Confirmar',
            ],
            'save' => [
                'label' => 'Salvar',
            ],
        ],
    ],
    
    'forms' => [
        'components' => [
            'repeater' => [
                'actions' => [
                    'add' => [
                        'label' => 'Adicionar :label',
                    ],
                    'clone' => [
                        'label' => 'Duplicar',
                    ],
                    'collapse' => [
                        'label' => 'Recolher',
                    ],
                    'expand' => [
                        'label' => 'Expandir',
                    ],
                    'delete' => [
                        'label' => 'Excluir',
                    ],
                    'move_down' => [
                        'label' => 'Mover para baixo',
                    ],
                    'move_up' => [
                        'label' => 'Mover para cima',
                    ],
                    'reorder' => [
                        'label' => 'Reordenar',
                    ],
                ],
            ],
        ],
    ],
];