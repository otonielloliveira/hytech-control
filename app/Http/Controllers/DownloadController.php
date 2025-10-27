<?php

namespace App\Http\Controllers;

use App\Models\Download;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DownloadController extends Controller
{
    public function index()
    {
        $downloads = Download::active()
                           ->byPriority()
                           ->paginate(20);
                           
        $categories = Download::getCategories();
        
        return view('downloads.index', compact('downloads', 'categories'));
    }

    public function show(Download $download)
    {
        if (!$download->is_active) {
            abort(404);
        }
        
        return view('downloads.show', compact('download'));
    }

    public function download(Download $download)
    {
        Log::info('Download request for: ' . $download->id);
        
        // Verificar se o download requer autenticação de cliente
        if ($download->requires_login && !Auth::guard('client')->check()) {
            Log::info('Client not authenticated for download: ' . $download->id);
            return redirect()->route('client.login')->with('error', 'Você precisa estar logado como cliente para fazer download deste arquivo.');
        }

        $filePath = $download->file_path;
        Log::info('Looking for file: ' . $filePath);
        
        if (!Storage::disk('public')->exists($filePath)) {
            Log::error('File not found: ' . $filePath);
            Log::info('Files in public disk: ' . json_encode(Storage::disk('public')->allFiles()));
            abort(404, 'Arquivo não encontrado: ' . $filePath);
        }

        // Incrementar contador de downloads
        $download->incrementDownloadCount();
        Log::info('Download count incremented for: ' . $download->id);

        $fileName = $download->title . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
        Log::info('Downloading file with name: ' . $fileName);

        return response()->download(storage_path('app/public/' . $filePath), $fileName);
    }

    public function category($category)
    {
        $downloads = Download::active()
                           ->byCategory($category)
                           ->byPriority()
                           ->paginate(20);
                           
        $categories = Download::getCategories();
        
        return view('downloads.category', compact('downloads', 'categories', 'category'));
    }
}
