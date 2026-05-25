<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User as Usuario;
use App\Models\Publicacao;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. CRIANDO USUÁRIOS DE TESTE (Caso ainda não existam no banco)
        // Usuário Cliente padrão
        $cliente = Usuario::where('email', 'cliente@instaclass.com')->first();
        if (!$cliente) {
            Usuario::create([
                'nome' => 'Usuário Teste',
                'nome_usuario' => 'usuarioteste',
                'email' => 'cliente@instaclass.com',
                'senha' => password_hash('123456', PASSWORD_DEFAULT),
                'tipo' => 'cliente'
            ]);
            $cliente = Usuario::where('email', 'cliente@instaclass.com')->first();
        }

        // Usuário Administrador padrão
        $admin = Usuario::where('email', 'admin@instaclass.com')->first();
        if (!$admin) {
            Usuario::create([
                'nome' => 'Admin Central',
                'nome_usuario' => 'admin',
                'email' => 'admin@instaclass.com',
                'senha' => password_hash('123456', PASSWORD_DEFAULT),
                'tipo' => 'admin'
            ]);
        }

        // 2. CRIANDO PUBLICAÇÕES DE TESTE (Vinculadas ao usuário cliente criado acima)
        // Deletamos publicações antigas da seed para não duplicar toda vez que rodar a rota
        Publicacao::where('legenda', 'LIKE', '%#Seed%')->delete();

        Publicacao::create([
            'usuario_id' => $cliente->id,
            'legenda' => 'Primeiro post oficial na plataforma! Que app incrível! #Seed',
            'url_imagem' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=600', // Imagem bonita de tecnologia
            'status' => 'aprovada' // <--- IMPORTANTE: 'aprovada' faz aparecer direto no feed
        ]);

        Publicacao::create([
            'usuario_id' => $cliente->id,
            'legenda' => 'Estudando desenvolvimento web com Laravel e PostgreSQL. 🚀 #Seed',
            'url_imagem' => 'https://images.unsplash.com/photo-1531403009284-440f080d1e12?w=600', // Imagem de código/design
            'status' => 'aprovada'
        ]);

        Publicacao::create([
            'usuario_id' => $cliente->id,
            'legenda' => 'Mais um dia de deploy concluído com sucesso no Render. #Seed',
            'url_imagem' => 'https://images.unsplash.com/photo-1618401471353-b98aedd07871?w=600', // Imagem de servidores/git
            'status' => 'aprovada'
        ]);
    }
}
