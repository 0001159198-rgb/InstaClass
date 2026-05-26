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
        // Limpa dados antigos do seed para evitar duplicações ou conflitos
        DB::table('publicacoes')->where('legenda', 'LIKE', '%#Seed%')->delete();
        DB::table('usuarios')->where('email', 'LIKE', '%@instaclass.com')->delete();

        // ==========================================
        // 1. CRIANDO AS CONTAS DE EXEMPLO (USUÁRIOS)
        // ==========================================

        // Conta 1: Carlos Silva (Desenvolvedor)
        $idCarlos = DB::table('usuarios')->insertGetId([
            'nome' => 'Carlos Silva',
            'nome_usuario' => 'carlos_dev',
            'email' => 'carlos@instaclass.com',
            'senha' => password_hash('123456', PASSWORD_DEFAULT),
            'tipo' => 'cliente'
        ]);

        // Conta 2: Mariana Costa (Fotógrafa)
        $idMariana = DB::table('usuarios')->insertGetId([
            'nome' => 'Mariana Costa',
            'nome_usuario' => 'mari_photos',
            'email' => 'mariana@instaclass.com',
            'senha' => password_hash('123456', PASSWORD_DEFAULT),
            'tipo' => 'cliente'
        ]);

        // Conta 3: Amanda Lima (UI/UX Designer)
        $idAmanda = DB::table('usuarios')->insertGetId([
            'nome' => 'Amanda Lima',
            'nome_usuario' => 'amanda_ux',
            'email' => 'amanda@instaclass.com',
            'senha' => password_hash('123456', PASSWORD_DEFAULT),
            'tipo' => 'cliente'
        ]);

        // Conta 4: Administrador do Sistema
        DB::table('usuarios')->insert([
            'nome' => 'Admin Central',
            'nome_usuario' => 'admin',
            'email' => 'admin@instaclass.com',
            'senha' => password_hash('123456', PASSWORD_DEFAULT),
            'tipo' => 'admin'
        ]);


        // ==========================================
        // 2. CRIANDO AS PUBLICAÇÕES DE CADA CONTA
        // ==========================================

        // Posts do Carlos (ID Carlos)
        DB::table('publicacoes')->insert([
            'usuario_id' => $idCarlos,
            'legenda' => 'Estudando desenvolvimento web com Laravel e PostgreSQL no Render. 🚀 #Seed',
            'url_imagem' => 'https://images.unsplash.com/photo-1531403009284-440f080d1e12?w=600',
            'status' => 'aprovada',
            'created_at' => now()->subHours(2),
            'updated_at' => now()->subHours(2)
        ]);

        // Posts da Mariana (ID Mariana)
        DB::table('publicacoes')->insert([
            'usuario_id' => $idMariana,
            'legenda' => 'Mais um dia capturando momentos incríveis pela cidade. 📷✨ #Seed',
            'url_imagem' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=600',
            'status' => 'aprovada',
            'created_at' => now()->subHours(1),
            'updated_at' => now()->subHours(1)
        ]);

        // Posts da Amanda (ID Amanda)
        DB::table('publicacoes')->insert([
            'usuario_id' => $idAmanda,
            'legenda' => 'Organizando o workflow e a paleta de cores para o próximo projeto mobile. 🎨 #Seed',
            'url_imagem' => 'https://images.unsplash.com/photo-1618401471353-b98aedd07871?w=600',
            'status' => 'aprovada',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
