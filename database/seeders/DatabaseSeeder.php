<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
        $adminPassword = env('ADMIN_PASSWORD');

        if (! $adminPassword) {
            if ($this->command) {
                $this->command->warn('ADMIN_PASSWORD is not set. Skipping super admin seed.');
            }
        } else {
            User::updateOrCreate(
                ['email' => $adminEmail],
                [
                    'name' => env('ADMIN_NAME', 'Super Admin'),
                    'password' => Hash::make($adminPassword),
                    'is_super_admin' => true,
                ]
            );
        }

        $categories = [
            [
                'name' => '美甲护理',
                'slug' => 'nail-care',
                'description' => '美甲工具、甲油和护理产品。',
            ],
            [
                'name' => '美容护肤',
                'slug' => 'beauty-care',
                'description' => '日常护肤与美容护理产品。',
            ],
            [
                'name' => '工具配件',
                'slug' => 'tools',
                'description' => '门店和个人使用的专业工具。',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }

        $nailCare = Category::where('slug', 'nail-care')->first();
        $beautyCare = Category::where('slug', 'beauty-care')->first();
        $tools = Category::where('slug', 'tools')->first();

        $products = [
            [
                'category_id' => $nailCare->id,
                'name' => 'Yisi 亮泽甲油胶',
                'slug' => 'yisi-gloss-gel-polish',
                'description' => '高亮持久，适合日常美甲和门店使用。',
                'price' => 69.00,
                'image_url' => 'https://images.unsplash.com/photo-1604654894610-df63bc536371?auto=format&fit=crop&w=900&q=80',
                'skus' => [
                    ['name' => '奶茶色', 'code' => 'GEL-MILK-01', 'stock' => 36],
                    ['name' => '樱桃红', 'code' => 'GEL-RED-02', 'stock' => 28],
                    ['name' => '裸粉色', 'code' => 'GEL-PINK-03', 'stock' => 42],
                ],
            ],
            [
                'category_id' => $nailCare->id,
                'name' => 'Yisi 猫眼磁吸甲油胶',
                'slug' => 'yisi-cat-eye-gel-polish',
                'description' => '细腻猫眼光泽，搭配磁棒可做出多种光影效果。',
                'price' => 79.00,
                'image_url' => 'https://images.unsplash.com/photo-1599948128020-9a44a50f1102?auto=format&fit=crop&w=900&q=80',
                'skus' => [
                    ['name' => '星河银', 'code' => 'CAT-SILVER-01', 'stock' => 34],
                    ['name' => '玫瑰金', 'code' => 'CAT-ROSE-02', 'stock' => 29],
                    ['name' => '黑曜石', 'code' => 'CAT-BLACK-03', 'stock' => 24],
                ],
            ],
            [
                'category_id' => $nailCare->id,
                'name' => 'Yisi 可卸底胶',
                'slug' => 'yisi-soak-off-base-coat',
                'description' => '提升甲油胶附着力，卸除更轻松。',
                'price' => 59.00,
                'image_url' => 'https://images.unsplash.com/photo-1604654894611-6973b376cbde?auto=format&fit=crop&w=900&q=80',
                'skus' => [
                    ['name' => '标准款 15ml', 'code' => 'BASE-15', 'stock' => 68],
                    ['name' => '加固款 15ml', 'code' => 'BASE-STRONG-15', 'stock' => 41],
                ],
            ],
            [
                'category_id' => $nailCare->id,
                'name' => 'Yisi 免洗封层',
                'slug' => 'yisi-no-wipe-top-coat',
                'description' => '免擦洗高亮封层，减少操作步骤。',
                'price' => 65.00,
                'image_url' => 'https://images.unsplash.com/photo-1610992015732-2449b76344bc?auto=format&fit=crop&w=900&q=80',
                'skus' => [
                    ['name' => '亮面 15ml', 'code' => 'TOP-GLOSS-15', 'stock' => 57],
                    ['name' => '磨砂 15ml', 'code' => 'TOP-MATTE-15', 'stock' => 39],
                ],
            ],
            [
                'category_id' => $nailCare->id,
                'name' => 'Yisi 指甲护理油',
                'slug' => 'yisi-cuticle-oil',
                'description' => '滋润甲缘，改善干燥倒刺，适合每日护理。',
                'price' => 49.00,
                'image_url' => 'https://images.unsplash.com/photo-1607006344380-b6775a0824a7?auto=format&fit=crop&w=900&q=80',
                'skus' => [
                    ['name' => '玫瑰香 10ml', 'code' => 'OIL-ROSE-10', 'stock' => 55],
                    ['name' => '柑橘香 10ml', 'code' => 'OIL-CITRUS-10', 'stock' => 48],
                    ['name' => '薰衣草 10ml', 'code' => 'OIL-LAVENDER-10', 'stock' => 33],
                ],
            ],
            [
                'category_id' => $nailCare->id,
                'name' => '水晶延长胶',
                'slug' => 'crystal-builder-gel',
                'description' => '适合延长、加固和修补甲面，流动性稳定。',
                'price' => 89.00,
                'image_url' => 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&w=900&q=80',
                'skus' => [
                    ['name' => '透明 30g', 'code' => 'BUILDER-CLEAR-30', 'stock' => 26],
                    ['name' => '浅粉 30g', 'code' => 'BUILDER-PINK-30', 'stock' => 31],
                ],
            ],
            [
                'category_id' => $beautyCare->id,
                'name' => '保湿修护手膜',
                'slug' => 'hydrating-hand-mask',
                'description' => '美甲后修护手部肌肤，补水保湿。',
                'price' => 39.00,
                'image_url' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=900&q=80',
                'skus' => [
                    ['name' => '单片装', 'code' => 'MASK-1PC', 'stock' => 90],
                    ['name' => '10 片装', 'code' => 'MASK-10PC', 'stock' => 35],
                ],
            ],
            [
                'category_id' => $beautyCare->id,
                'name' => '柔润护手霜',
                'slug' => 'soft-hand-cream',
                'description' => '轻盈不粘腻，适合美甲后手部护理。',
                'price' => 45.00,
                'image_url' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&w=900&q=80',
                'skus' => [
                    ['name' => '白茶香 50g', 'code' => 'CREAM-WHITE-TEA', 'stock' => 73],
                    ['name' => '樱花香 50g', 'code' => 'CREAM-SAKURA', 'stock' => 64],
                ],
            ],
            [
                'category_id' => $beautyCare->id,
                'name' => '手部细致磨砂膏',
                'slug' => 'hand-smoothing-scrub',
                'description' => '温和去角质，让手部肌肤更细腻。',
                'price' => 58.00,
                'image_url' => 'https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?auto=format&fit=crop&w=900&q=80',
                'skus' => [
                    ['name' => '蜜桃香 120g', 'code' => 'SCRUB-PEACH-120', 'stock' => 38],
                    ['name' => '海盐香 120g', 'code' => 'SCRUB-SALT-120', 'stock' => 32],
                ],
            ],
            [
                'category_id' => $beautyCare->id,
                'name' => '甲面清洁棉片',
                'slug' => 'nail-cleaning-pads',
                'description' => '用于甲面清洁、去浮胶和工具擦拭。',
                'price' => 29.00,
                'image_url' => 'https://images.unsplash.com/photo-1583947215259-38e31be8751f?auto=format&fit=crop&w=900&q=80',
                'skus' => [
                    ['name' => '100 片装', 'code' => 'PAD-100', 'stock' => 120],
                    ['name' => '300 片装', 'code' => 'PAD-300', 'stock' => 58],
                ],
            ],
            [
                'category_id' => $tools->id,
                'name' => '专业美甲笔刷套装',
                'slug' => 'professional-nail-brush-set',
                'description' => '含线条笔、晕染笔、雕花笔，适合精细图案。',
                'price' => 129.00,
                'image_url' => 'https://images.unsplash.com/photo-1519014816548-bf5fe059798b?auto=format&fit=crop&w=900&q=80',
                'skus' => [
                    ['name' => '5 支套装', 'code' => 'BRUSH-5', 'stock' => 30],
                    ['name' => '9 支套装', 'code' => 'BRUSH-9', 'stock' => 22],
                ],
            ],
            [
                'category_id' => $tools->id,
                'name' => 'UV LED 光疗灯',
                'slug' => 'uv-led-nail-lamp',
                'description' => '多档定时，适合甲油胶、延长胶和封层固化。',
                'price' => 199.00,
                'image_url' => 'https://images.unsplash.com/photo-1606813902913-b4b3a1f2a6ff?auto=format&fit=crop&w=900&q=80',
                'skus' => [
                    ['name' => '48W 白色', 'code' => 'LAMP-48W-WHITE', 'stock' => 18],
                    ['name' => '72W 专业款', 'code' => 'LAMP-72W-PRO', 'stock' => 12],
                ],
            ],
            [
                'category_id' => $tools->id,
                'name' => '电动打磨机',
                'slug' => 'electric-nail-drill',
                'description' => '低噪稳定，适合卸甲、修型和精细打磨。',
                'price' => 269.00,
                'image_url' => 'https://images.unsplash.com/photo-1580618672591-eb180b1a973f?auto=format&fit=crop&w=900&q=80',
                'skus' => [
                    ['name' => '基础款', 'code' => 'DRILL-BASIC', 'stock' => 16],
                    ['name' => '门店款', 'code' => 'DRILL-SALON', 'stock' => 10],
                ],
            ],
            [
                'category_id' => $tools->id,
                'name' => '死皮剪套装',
                'slug' => 'cuticle-nipper-set',
                'description' => '不锈钢材质，含死皮剪、推棒和收纳盒。',
                'price' => 79.00,
                'image_url' => 'https://images.unsplash.com/photo-1522338242992-e1a54906a8da?auto=format&fit=crop&w=900&q=80',
                'skus' => [
                    ['name' => '银色', 'code' => 'NIPPER-SILVER', 'stock' => 45],
                    ['name' => '玫瑰金', 'code' => 'NIPPER-ROSE', 'stock' => 37],
                ],
            ],
            [
                'category_id' => $tools->id,
                'name' => '抛光打磨块',
                'slug' => 'buffer-block-set',
                'description' => '四面不同粗细，适合修整甲面和抛光。',
                'price' => 19.00,
                'image_url' => 'https://images.unsplash.com/photo-1632345031435-8727f6897d53?auto=format&fit=crop&w=900&q=80',
                'skus' => [
                    ['name' => '单个装', 'code' => 'BUFFER-1', 'stock' => 150],
                    ['name' => '10 个装', 'code' => 'BUFFER-10', 'stock' => 80],
                ],
            ],
            [
                'category_id' => $tools->id,
                'name' => '甲片展示板',
                'slug' => 'nail-display-board',
                'description' => '适合展示色卡、款式和门店作品。',
                'price' => 35.00,
                'image_url' => 'https://images.unsplash.com/photo-1610992015732-2449b76344bc?auto=format&fit=crop&w=900&q=80',
                'skus' => [
                    ['name' => '透明 50 片', 'code' => 'DISPLAY-CLEAR-50', 'stock' => 66],
                    ['name' => '黑色 50 片', 'code' => 'DISPLAY-BLACK-50', 'stock' => 49],
                ],
            ],
        ];

        foreach ($products as $productData) {
            $skus = $productData['skus'];
            unset($productData['skus']);

            $productData['stock'] = collect($skus)->sum('stock');
            $productData['is_active'] = true;

            $product = Product::updateOrCreate(['slug' => $productData['slug']], $productData);

            $product->images()->updateOrCreate(
                ['sort_order' => 0],
                [
                    'image_url' => $productData['image_url'],
                    'alt_text' => $productData['name'],
                ]
            );

            $product->images()->updateOrCreate(
                ['sort_order' => 1],
                [
                    'image_url' => $productData['image_url'] . '&sat=-30',
                    'alt_text' => $productData['name'] . ' 细节图',
                ]
            );

            foreach (['USD' => round($productData['price'] / 7.2, 2), 'HKD' => round($productData['price'] / 0.92, 2), 'CUP' => round($productData['price'] * 3.4, 2)] as $currency => $price) {
                $product->prices()->updateOrCreate(
                    ['currency_code' => $currency],
                    ['price' => $price]
                );
            }

            foreach ($skus as $index => $sku) {
                $product->skus()->updateOrCreate(
                    ['code' => $sku['code']],
                    [
                        'name' => $sku['name'],
                        'stock' => $sku['stock'],
                        'sort_order' => $index,
                        'is_active' => true,
                    ]
                );
            }
        }

        Product::doesntHave('skus')->get()->each(function (Product $product) {
            $product->skus()->create([
                'name' => '默认规格',
                'code' => 'DEFAULT-' . $product->id,
                'stock' => $product->stock,
                'sort_order' => 0,
                'is_active' => true,
            ]);
        });
    }
}
