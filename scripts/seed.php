<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        echo "<h1>🌱 Inserindo Dados Iniciais (Seed)</h1>";

        try {
            echo "<p>🧹 Limpando dados antigos...</p>";
            
            // Sintaxe correta para truncar tabelas com chaves estrangeiras no PostgreSQL
            DB::statement('TRUNCATE TABLE curtidas, denuncias, publicacoes, usuarios RESTART IDENTITY CASCADE');
            
            echo "<p style='color:green'>✅ Dados antigos removidos!</p>";
            
            // ================== 1. CRIAR 5 USUÁRIOS ==================
            echo "<h2>👥 Criando 5 usuários...</h2>";
            
            $usuarios = [
                ['nome' => 'Administrador', 'nome_usuario' => 'admin', 'email' => 'admin@instaclass.com', 'senha' => Hash::make('123456'), 'tipo' => 'admin'],
                ['nome' => 'João Silva', 'nome_usuario' => 'joaosilva', 'email' => 'joao@email.com', 'senha' => Hash::make('123456'), 'tipo' => 'cliente'],
                ['nome' => 'Maria Oliveira', 'nome_usuario' => 'mariaoliveira', 'email' => 'maria@email.com', 'senha' => Hash::make('123456'), 'tipo' => 'cliente'],
                ['nome' => 'Carlos Santos', 'nome_usuario' => 'carlossantos', 'email' => 'carlos@email.com', 'senha' => Hash::make('123456'), 'tipo' => 'cliente'],
                ['nome' => 'Ana Pereira', 'nome_usuario' => 'anapereira', 'email' => 'ana@email.com', 'senha' => Hash::make('123456'), 'tipo' => 'cliente']
            ];
            
            $usuariosIds = [];
            foreach ($usuarios as $user) {
                $id = DB::table('usuarios')->insertGetId([
                    'nome' => $user['nome'],
                    'nome_usuario' => $user['nome_usuario'],
                    'email' => $user['email'],
                    'senha' => $user['senha'],
                    'tipo' => $user['tipo'],
                    'criado_em' => now()
                ]);
                $usuariosIds[$user['nome_usuario']] = $id;
                echo "<p>✅ Usuário criado: {$user['nome']} (@{$user['nome_usuario']}) - ID: {$id}</p>";
            }
            
            // ================== 2. CRIAR 10 PUBLICAÇÕES ==================
            echo "<h2>📷 Criando 10 publicações...</h2>";
            
            // Mapeia os posts dinamicamente para os IDs reais gerados acima
            $publicacoes = [
                ['usuario_id' => $usuariosIds['joaosilva'], 'legenda' => 'Meu primeiro post no InstaClass! 🎉 Bem-vindos!', 'url_imagem' => 'https://picsum.photos/id/1/500/500', 'status' => 'aprovado'],
                ['usuario_id' => $usuariosIds['joaosilva'], 'legenda' => 'Hoje está um dia ensolarado! ☀️ Aproveitando a vida.', 'url_imagem' => 'https://picsum.photos/id/2/500/500', 'status' => 'aprovado'],
                ['usuario_id' => $usuariosIds['mariaoliveira'], 'legenda' => 'Aprendendo PHP e criando uma rede social! 💻 Muito legal!', 'url_imagem' => 'https://picsum.photos/id/3/500/500', 'status' => 'aprovado'],
                ['usuario_id' => $usuariosIds['mariaoliveira'], 'legenda' => 'Compartilhando um momento especial com vocês ❤️', 'url_imagem' => 'https://picsum.photos/id/4/500/500', 'status' => 'aprovado'],
                ['usuario_id' => $usuariosIds['carlossantos'], 'legenda' => 'Dica de hoje: Estudem programação! Vale muito a pena! 🚀', 'url_imagem' => 'https://picsum.photos/id/5/500/500', 'status' => 'aprovado'],
                ['usuario_id' => $usuariosIds['carlossantos'], 'legenda' => 'Praia no fim de semana 🏖️ Recarregando as energias!', 'url_imagem' => 'https://picsum.photos/id/6/500/500', 'status' => 'aprovado'],
                ['usuario_id' => $usuariosIds['anapereira'], 'legenda' => 'Novo projeto incrível em breve! Aguardem! 🔥', 'url_imagem' => 'https://picsum.photos/id/7/500/500', 'status' => 'aprovado'],
                ['usuario_id' => $usuariosIds['anapereira'], 'legenda' => 'Café e código ☕️ A melhor combinação!', 'url_imagem' => 'https://picsum.photos/id/8/500/500', 'status' => 'pendente'],
                ['usuario_id' => $usuariosIds['joaosilva'], 'legenda' => 'Final de semana de estudos! 📚 Quem mais?', 'url_imagem' => 'https://picsum.photos/id/9/500/500', 'status' => 'aprovado'],
                ['usuario_id' => $usuariosIds['mariaoliveira'], 'legenda' => 'Música nova no estilo! 🎵 Recomendo demais!', 'url_imagem' => 'https://picsum.photos/id/10/500/500', 'status' => 'aprovado']
            ];
            
            $publicacoesIds = [];
            foreach ($publicacoes as $index => $pub) {
                $id = DB::table('publicacoes')->insertGetId([
                    'usuario_id' => $pub['usuario_id'],
                    'legenda' => $pub['legenda'],
                    'url_imagem' => $pub['url_imagem'],
                    'status' => $pub['status'],
                    'total_curtidas' => 0,
                    'total_comentarios' => 0,
                    'criado_em' => now()
                ]);
                $publicacoesIds[$index + 1] = $id;
                echo "<p>✅ Publicação criada: ID {$id} - (Status: {$pub['status']})</p>";
            }
            
            // ================== 3. CRIAR 10 CURTIDAS ==================
            echo "<h2>❤️ Criando 10 curtidas...</h2>";
            
            $curtidas = [
                ['usuario_id' => $usuariosIds['joaosilva'], 'publicacao_index' => 1],
                ['usuario_id' => $usuariosIds['joaosilva'], 'publicacao_index' => 2],
                ['usuario_id' => $usuariosIds['mariaoliveira'], 'publicacao_index' => 1],
                ['usuario_id' => $usuariosIds['mariaoliveira'], 'publicacao_index' => 3],
                ['usuario_id' => $usuariosIds['carlossantos'], 'publicacao_index' => 2],
                ['usuario_id' => $usuariosIds['carlossantos'], 'publicacao_index' => 4],
                ['usuario_id' => $usuariosIds['anapereira'], 'publicacao_index' => 1],
                ['usuario_id' => $usuariosIds['anapereira'], 'publicacao_index' => 5],
                ['usuario_id' => $usuariosIds['joaosilva'], 'publicacao_index' => 6],
                ['usuario_id' => $usuariosIds['mariaoliveira'], 'publicacao_index' => 7]
            ];
            
            foreach ($curtidas as $curtida) {
                $pubId = $publicacoesIds[$curtida['publicacao_index']];
                
                DB::table('curtidas')->insert([
                    'usuario_id' => $curtida['usuario_id'],
                    'publicacao_id' => $pubId,
                    'criado_em' => now()
                ]);
                
                DB::table('publicacoes')->where('id', $pubId)->increment('total_curtidas');
                echo "<p>✅ Curtida: Usuário ID {$curtida['usuario_id']} → Publicacao ID {$pubId}</p>";
            }
            
            // ================== 4. CRIAR 3 DENÚNCIAS ==================
            echo "<h2>🚨 Criando 3 denúncias...</h2>";
            
            $denuncias = [
                ['publicacao_id' => $publicacoesIds[2], 'motivo' => 'Conteúdo impróprio', 'gravidade' => 'media'],
                ['publicacao_id' => $publicacoesIds[5], 'motivo' => 'Discurso de ódio', 'gravidade' => 'alta'],
                ['publicacao_id' => $publicacoesIds[8], 'motivo' => 'Spam ou enganoso', 'gravidade' => 'baixa']
            ];
            
            foreach ($denuncias as $denuncia) {
                DB::table('denuncias')->insert([
                    'publicacao_id' => $denuncia['publicacao_id'],
                    'motivo' => $denuncia['motivo'],
                    'gravidade' => $denuncia['gravidade'],
                    'status' => 'pendente',
                    'criado_em' => now()
                ]);
                echo "<p>✅ Denúncia: Publicação ID {$denuncia['publicacao_id']} - Motivo: {$denuncia['motivo']}</p>";
            }
            
            echo "<p style='color:green; font-size:18px;'>✅ Seed concluído com sucesso!</p>";
            
        } catch (\Exception $e) {
            echo "<p style='color:red'>❌ Erro: " . $e->getMessage() . "</p>";
        }
    }
}