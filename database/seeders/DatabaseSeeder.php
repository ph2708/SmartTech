<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create SmartTech tenant
        $tenant = Tenant::create([
            'name' => 'Smart Tech',
            'slug' => 'smarttech',
            'whatsapp' => '6499249-5817',
            'description' => 'Assistência Técnica e Acessórios. Consertamos celulares e computadores, vendemos capinhas, acessórios e perfumes.',
            'primary_color' => '#e63946',
            'secondary_color' => '#1d1d1d',
            'is_active' => true,
            'plan' => 'pro',
        ]);

        // Create super admin
        User::create([
            'name' => 'Admin SmartTech',
            'email' => 'admin@smarttech.com',
            'password' => Hash::make('123456'),
            'tenant_id' => null,
            'role' => 'super_admin',
        ]);

        // Create tenant admin
        User::create([
            'name' => 'Smart Tech',
            'email' => 'smarttech@smarttech.com',
            'password' => Hash::make('123456'),
            'tenant_id' => $tenant->id,
            'role' => 'admin',
        ]);

        // Create categories
        $categories = [
            ['name' => 'Assistência Técnica - Celulares', 'slug' => 'assistencia-celulares', 'icon' => '📱', 'order' => 1, 'description' => 'Conserto e manutenção de celulares de todas as marcas'],
            ['name' => 'Assistência Técnica - Computadores', 'slug' => 'assistencia-computadores', 'icon' => '💻', 'order' => 2, 'description' => 'Formatação, manutenção e reparo de computadores e notebooks'],
            ['name' => 'Capinhas e Películas', 'slug' => 'capinhas-peliculas', 'icon' => '🛡️', 'order' => 3, 'description' => 'Capinhas e películas para proteção do seu celular'],
            ['name' => 'Acessórios', 'slug' => 'acessorios', 'icon' => '🎧', 'order' => 4, 'description' => 'Fones de ouvido, cabos, carregadores e mais'],
            ['name' => 'Perfumes', 'slug' => 'perfumes', 'icon' => '🧴', 'order' => 5, 'description' => 'Perfumes importados e nacionais'],
        ];

        $createdCategories = [];
        foreach ($categories as $cat) {
            $createdCategories[$cat['slug']] = Category::create($cat + ['tenant_id' => $tenant->id]);
        }

        // Sample products
        $products = [
            // Assistência Técnica - Celulares
            ['category' => 'assistencia-celulares', 'name' => 'Troca de Tela - iPhone', 'price' => 250.00, 'description' => 'Serviço de troca de tela para todos os modelos de iPhone. Peças originais e garantia de 3 meses.', 'is_featured' => true],
            ['category' => 'assistencia-celulares', 'name' => 'Troca de Tela - Samsung', 'price' => 200.00, 'description' => 'Serviço de troca de tela para smartphones Samsung. Garantia de 3 meses.'],
            ['category' => 'assistencia-celulares', 'name' => 'Troca de Bateria', 'price' => 120.00, 'description' => 'Substituição de bateria para celulares de todas as marcas.'],
            ['category' => 'assistencia-celulares', 'name' => 'Reparo de Conector de Carga', 'price' => 100.00, 'description' => 'Conserto do conector de carga do seu celular.'],

            // Assistência Técnica - Computadores
            ['category' => 'assistencia-computadores', 'name' => 'Formatação Completa', 'price' => 150.00, 'description' => 'Formatação completa com instalação do Windows, drivers e programas essenciais.', 'is_featured' => true],
            ['category' => 'assistencia-computadores', 'name' => 'Limpeza e Manutenção', 'price' => 100.00, 'description' => 'Limpeza interna, troca de pasta térmica e otimização do sistema.'],
            ['category' => 'assistencia-computadores', 'name' => 'Upgrade SSD', 'price' => 250.00, 'description' => 'Instalação de SSD com migração de dados. Deixe seu PC muito mais rápido!'],

            // Capinhas
            ['category' => 'capinhas-peliculas', 'name' => 'Capinha Anti-impacto iPhone 15', 'price' => 49.90, 'promotional_price' => 39.90, 'description' => 'Capinha anti-impacto transparente para iPhone 15. Proteção militar.', 'is_featured' => true],
            ['category' => 'capinhas-peliculas', 'name' => 'Capinha Silicone Samsung S24', 'price' => 39.90, 'description' => 'Capinha de silicone macia para Samsung Galaxy S24.'],
            ['category' => 'capinhas-peliculas', 'name' => 'Película de Vidro 3D', 'price' => 29.90, 'promotional_price' => 19.90, 'description' => 'Película de vidro temperado 3D para diversos modelos.'],

            // Acessórios
            ['category' => 'acessorios', 'name' => 'Fone Bluetooth TWS', 'price' => 89.90, 'promotional_price' => 69.90, 'description' => 'Fone de ouvido Bluetooth TWS com cancelamento de ruído. Bateria de longa duração.', 'is_featured' => true],
            ['category' => 'acessorios', 'name' => 'Carregador Turbo USB-C', 'price' => 59.90, 'description' => 'Carregador turbo 33W USB-C para carregamento rápido.'],
            ['category' => 'acessorios', 'name' => 'Cabo USB-C 2 metros', 'price' => 29.90, 'description' => 'Cabo USB-C reforçado de 2 metros com revestimento em nylon.'],
            ['category' => 'acessorios', 'name' => 'Suporte Veicular Magnético', 'price' => 49.90, 'description' => 'Suporte magnético para celular no carro. Compatível com todos os modelos.'],

            // Perfumes
            ['category' => 'perfumes', 'name' => 'Perfume Masculino 100ml - Essência Premium', 'price' => 120.00, 'promotional_price' => 99.90, 'description' => 'Perfume masculino com fragrância amadeirada. Longa duração.', 'is_featured' => true],
            ['category' => 'perfumes', 'name' => 'Perfume Feminino 100ml - Floral', 'price' => 130.00, 'description' => 'Perfume feminino com fragrância floral suave e sofisticada.'],
            ['category' => 'perfumes', 'name' => 'Kit Perfume + Hidratante', 'price' => 180.00, 'promotional_price' => 149.90, 'description' => 'Kit especial com perfume 100ml e hidratante corporal 200ml.'],
        ];

        foreach ($products as $index => $productData) {
            $categorySlug = $productData['category'];
            unset($productData['category']);

            Product::create(array_merge($productData, [
                'tenant_id' => $tenant->id,
                'category_id' => $createdCategories[$categorySlug]->id,
                'slug' => \Illuminate\Support\Str::slug($productData['name']),
                'is_active' => true,
                'is_featured' => $productData['is_featured'] ?? false,
                'order' => $index,
            ]));
        }
    }
}
