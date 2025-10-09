<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PollController extends Controller
{
    public function vote(Request $request, Poll $poll): JsonResponse
    {
        // Verificar se a enquete pode receber votos
        if (!$poll->canVote()) {
            return response()->json([
                'success' => false,
                'message' => 'Esta enquete não está mais disponível para votação.'
            ], 400);
        }

        // Validar dados
        $request->validate([
            'option_id' => 'required|exists:blog_poll_options,id'
        ]);

        $optionId = $request->option_id;
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        // Verificar se a opção pertence à enquete
        $option = PollOption::where('id', $optionId)
                           ->where('poll_id', $poll->id)
                           ->first();

        if (!$option) {
            return response()->json([
                'success' => false,
                'message' => 'Opção inválida para esta enquete.'
            ], 400);
        }

        // Verificar se já votou nesta enquete
        if (PollVote::hasVotedInPoll($poll->id, $ipAddress)) {
            return response()->json([
                'success' => false,
                'message' => 'Você já votou nesta enquete.'
            ], 400);
        }

        // Adicionar o voto
        $voted = $option->addVote($ipAddress, $userAgent);

        if ($voted) {
            return response()->json([
                'success' => true,
                'message' => 'Voto registrado com sucesso!',
                'poll' => [
                    'id' => $poll->id,
                    'total_votes' => $poll->fresh()->total_votes
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Erro ao registrar o voto. Tente novamente.'
        ], 500);
    }

    public function results(Poll $poll)
    {
        $poll->load('options');
        
        return response()->json([
            'poll' => [
                'id' => $poll->id,
                'title' => $poll->title,
                'description' => $poll->description,
                'total_votes' => $poll->total_votes,
                'options' => $poll->options->map(function ($option) {
                    return [
                        'id' => $option->id,
                        'text' => $option->option_text,
                        'votes' => $option->votes_count,
                        'percentage' => $option->vote_percentage
                    ];
                })
            ]
        ]);
    }

    public function revote(Request $request, Poll $poll): JsonResponse
    {
        // Verificar se a enquete pode receber votos
        if (!$poll->canVote()) {
            return response()->json([
                'success' => false,
                'message' => 'Esta enquete não está mais disponível para votação.'
            ], 400);
        }

        // Validar dados
        $request->validate([
            'option_id' => 'required|exists:blog_poll_options,id'
        ]);

        $optionId = $request->option_id;
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        // Verificar se a opção pertence à enquete
        $option = PollOption::where('id', $optionId)
                           ->where('poll_id', $poll->id)
                           ->first();

        if (!$option) {
            return response()->json([
                'success' => false,
                'message' => 'Opção inválida para esta enquete.'
            ], 400);
        }

        // Remover voto anterior se existir
        PollVote::removeVoteFromPoll($poll->id, $ipAddress);

        // Adicionar o novo voto
        $voted = $option->addVote($ipAddress, $userAgent);

        if ($voted) {
            return response()->json([
                'success' => true,
                'message' => 'Voto alterado com sucesso!',
                'poll' => [
                    'id' => $poll->id,
                    'total_votes' => $poll->fresh()->total_votes
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Erro ao alterar o voto. Tente novamente.'
        ], 500);
    }
}