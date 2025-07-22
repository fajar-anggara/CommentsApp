<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant1 = Tenant::where('domain', 'tenantone.localhost')->first();
        $tenant2 = Tenant::where('domain', 'tenanttwo.localhost')->first();

        if ($tenant1) {
            Article::create([
                'tenant_id' => $tenant1->id,
                'external_article_id' => 'T1A1',
                'title' => 'First Article for Tenant One',
                'url' => 'http://tenantone.localhost/articles/1',
            ]);

            Article::create([
                'tenant_id' => $tenant1->id,
                'external_article_id' => 'T1A2',
                'title' => 'Second Article for Tenant One',
                'url' => 'http://tenantone.localhost/articles/2',
            ]);
        }

        if ($tenant2) {
            Article::create([
                'tenant_id' => $tenant2->id,
                'external_article_id' => 'T2A1',
                'title' => 'First Article for Tenant Two',
                'url' => 'http://tenanttwo.localhost/articles/1',
            ]);

            Article::create([
                'tenant_id' => $tenant2->id,
                'external_article_id' => 'T2A2',
                'title' => 'Second Article for Tenant Two',
                'url' => 'http://tenanttwo.localhost/articles/2',
            ]);
        }
    }
}
