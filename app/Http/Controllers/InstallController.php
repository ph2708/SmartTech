<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class InstallController extends Controller
{
    public function index()
    {
        // Se a tabela users já existir com usuários, bloqueia acesso público ao /install por segurança
        if ($this->isAlreadyInstalled()) {
            return response('<h3>🔒 O sistema já foi instalado com sucesso!</h3><p>Para sua segurança, o assistente foi bloqueado. Acesse <a href="/login">/login</a> ou exclua o arquivo de trava para reconfigurar.</p>', 403);
        }

        $dbConnected = false;
        $dbError = null;

        try {
            DB::connection()->getPdo();
            $dbConnected = true;
        } catch (\Exception $e) {
            $dbError = $e->getMessage();
        }

        $phpVersion = phpversion();
        $phpOk = version_compare($phpVersion, '8.2.0', '>=');

        $storageWritable = is_writable(storage_path());
        $cacheWritable = is_writable(bootstrap_path('cache'));

        return view('install.index', compact('dbConnected', 'dbError', 'phpVersion', 'phpOk', 'storageWritable', 'cacheWritable'));
    }

    public function run(Request $request)
    {
        if ($this->isAlreadyInstalled()) {
            abort(403, 'Instalação bloqueada.');
        }

        $action = $request->input('action');
        $output = '';

        try {
            if ($action === 'migrate_seed') {
                Artisan::call('migrate --force --seed');
                $output = Artisan::output();
                @file_put_contents(storage_path('installed.lock'), now()->toDateTimeString());
            } elseif ($action === 'storage_link') {
                Artisan::call('storage:link');
                $output = Artisan::output();
            } elseif ($action === 'clear_cache') {
                Artisan::call('optimize:clear');
                $output = Artisan::output();
            } else {
                $output = 'Ação desconhecida.';
            }

            return back()->with('success', $output);
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao executar: ' . $e->getMessage());
        }
    }

    protected function isAlreadyInstalled(): bool
    {
        if (file_exists(storage_path('installed.lock'))) {
            return true;
        }

        try {
            return DB::table('users')->count() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
}
