<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. CRIANDO USUÁRIOS DE TESTE (Direto na tabela 'usuarios')
        
        // Verifica se o usuário cliente já existe
        $cliente = DB::table('usuarios')->where('email', 'cliente@instaclass.com')->first();
        
        if (!$cliente) {
            DB::table('usuarios')->insert([
                'nome' => 'Usuário Teste',
                'nome_usuario' => 'usuarioteste',
                'email' => 'cliente@instaclass.com',
                'senha' => password_hash('123456', PASSWORD_DEFAULT),
                'tipo' => 'cliente',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            // Recupera o ID recém-criado
            $cliente = DB::table('usuarios')->where('email', 'cliente@instaclass.com')->first();
        }

        // Verifica se o usuário administrador já existe
        $admin = DB::table('usuarios')->where('email', 'admin@instaclass.com')->first();
        
        if (!$admin) {
            DB::table('usuarios')->insert([
                'nome' => 'Admin Central',
                'nome_usuario' => 'admin',
                'email' => 'admin@instaclass.com',
                'senha' => password_hash('123456', PASSWORD_DEFAULT),
                'tipo' => 'admin',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 2. CRIANDO PUBLICAÇÕES DE TESTE (Direto na tabela 'publicacoes')
        
        // Remove publicações antigas do seed para evitar duplicações
        DB::table('publicacoes')->where('legenda', 'LIKE', '%#Seed%')->delete();

        if ($cliente) {
            // Post 1
            DB::table('publicacoes')->insert([
                'usuario_id' => $cliente->id,
                'legenda' => 'Primeiro post oficial na plataforma! Que app incrível! #Seed',
                'url_imagem' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=600',
                'status' => 'aprovada', // Obrigatoriamente 'aprovada'
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Post 2
            DB::table('publicacoes')->insert([
                'usuario_id' => $cliente->id,
                'legenda' => 'Estudando desenvolvimento web com Laravel e PostgreSQL. 🚀 #Seed',
                'url_imagem' => 'https://images.unsplash.com/photo-1531403009284-440f080d1e12?w=600',
                'status' => 'aprovada',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Post 3
            DB::table('publicacoes')->insert([
                'usuario_id' => $cliente->id,
                'legenda' => 'Mais um dia de deploy concluído com sucesso no Render. #Seed',
                'url_imagem' => 'https://images.unsplash.com/photo-1618401471353-b98aedd07871?w=600',
                'status' => 'aprovada',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
