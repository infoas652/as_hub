<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PricingPlan;

class PricingPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing pricing plans
        PricingPlan::truncate();

        // English Pricing Plans
        $englishPlans = [
            // Website Development Plans
            [
                'language' => 'en',
                'service_type' => 'website',
                'tier' => 'basic',
                'name' => 'Basic Website',
                'slug' => 'basic-website',
                'description' => 'Perfect for small businesses and startups',
                'price_monthly' => 299,
                'price_yearly' => 2990,
                'features' => [
                    'Up to 5 pages',
                    'Responsive design',
                    'Basic SEO optimization',
                    'Contact form',
                    '1 month support',
                    'SSL certificate'
                ],
                'is_popular' => false,
                'order' => 1,
                'is_active' => true,
            ],
            [
                'language' => 'en',
                'service_type' => 'website',
                'tier' => 'professional',
                'name' => 'Professional Website',
                'slug' => 'professional-website',
                'description' => 'Ideal for growing businesses',
                'price_monthly' => 599,
                'price_yearly' => 5990,
                'features' => [
                    'Up to 15 pages',
                    'Advanced responsive design',
                    'Advanced SEO optimization',
                    'Multiple contact forms',
                    'Blog integration',
                    '3 months support',
                    'SSL certificate',
                    'Google Analytics'
                ],
                'is_popular' => true,
                'order' => 2,
                'is_active' => true,
            ],
            [
                'language' => 'en',
                'service_type' => 'website',
                'tier' => 'enterprise',
                'name' => 'Enterprise Website',
                'slug' => 'enterprise-website',
                'description' => 'For large organizations',
                'price_monthly' => 1299,
                'price_yearly' => 12990,
                'features' => [
                    'Unlimited pages',
                    'Custom design',
                    'Premium SEO optimization',
                    'Advanced forms & integrations',
                    'CMS integration',
                    'E-commerce ready',
                    '6 months support',
                    'SSL certificate',
                    'Advanced analytics',
                    'Priority support'
                ],
                'is_popular' => false,
                'order' => 3,
                'is_active' => true,
            ],

            // Mobile App Development Plans
            [
                'language' => 'en',
                'service_type' => 'app',
                'tier' => 'basic',
                'name' => 'Basic Mobile App',
                'slug' => 'basic-mobile-app',
                'description' => 'Simple app for iOS or Android',
                'price_monthly' => 499,
                'price_yearly' => 4990,
                'features' => [
                    'Single platform (iOS or Android)',
                    'Up to 5 screens',
                    'Basic UI/UX design',
                    'Push notifications',
                    'Basic analytics',
                    '1 month support',
                    'App store submission'
                ],
                'is_popular' => false,
                'order' => 4,
                'is_active' => true,
            ],
            [
                'language' => 'en',
                'service_type' => 'app',
                'tier' => 'professional',
                'name' => 'Professional Mobile App',
                'slug' => 'professional-mobile-app',
                'description' => 'Full-featured cross-platform app',
                'price_monthly' => 999,
                'price_yearly' => 9990,
                'features' => [
                    'Cross-platform (iOS & Android)',
                    'Up to 15 screens',
                    'Custom UI/UX design',
                    'Push notifications',
                    'In-app purchases',
                    'API integration',
                    'Advanced analytics',
                    '3 months support',
                    'App store submission'
                ],
                'is_popular' => true,
                'order' => 5,
                'is_active' => true,
            ],
            [
                'language' => 'en',
                'service_type' => 'app',
                'tier' => 'enterprise',
                'name' => 'Enterprise Mobile App',
                'slug' => 'enterprise-mobile-app',
                'description' => 'Enterprise-grade mobile solution',
                'price_monthly' => 2499,
                'price_yearly' => 24990,
                'features' => [
                    'Cross-platform (iOS & Android)',
                    'Unlimited screens',
                    'Premium custom design',
                    'Advanced features',
                    'Backend development',
                    'Third-party integrations',
                    'Real-time features',
                    'Advanced security',
                    'Premium analytics',
                    '6 months support',
                    'App store optimization',
                    'Priority support'
                ],
                'is_popular' => false,
                'order' => 6,
                'is_active' => true,
            ],

            // Website + App Package Plans
            [
                'language' => 'en',
                'service_type' => 'both',
                'tier' => 'basic',
                'name' => 'Basic Package',
                'slug' => 'basic-package',
                'description' => 'Website + Mobile app combo',
                'price_monthly' => 699,
                'price_yearly' => 6990,
                'features' => [
                    'Basic website (5 pages)',
                    'Basic mobile app (single platform)',
                    'Responsive design',
                    'Basic SEO',
                    'Push notifications',
                    'Contact forms',
                    '1 month support',
                    'SSL certificate',
                    'App store submission'
                ],
                'is_popular' => false,
                'order' => 7,
                'is_active' => true,
            ],
            [
                'language' => 'en',
                'service_type' => 'both',
                'tier' => 'professional',
                'name' => 'Professional Package',
                'slug' => 'professional-package',
                'description' => 'Complete digital presence',
                'price_monthly' => 1399,
                'price_yearly' => 13990,
                'features' => [
                    'Professional website (15 pages)',
                    'Cross-platform mobile app',
                    'Custom design',
                    'Advanced SEO',
                    'API integration',
                    'Push notifications',
                    'In-app purchases',
                    'Blog integration',
                    'Advanced analytics',
                    '3 months support',
                    'SSL certificate',
                    'App store submission'
                ],
                'is_popular' => true,
                'order' => 8,
                'is_active' => true,
            ],
            [
                'language' => 'en',
                'service_type' => 'both',
                'tier' => 'enterprise',
                'name' => 'Enterprise Package',
                'slug' => 'enterprise-package',
                'description' => 'Complete enterprise solution',
                'price_monthly' => 2999,
                'price_yearly' => 29990,
                'features' => [
                    'Enterprise website (unlimited pages)',
                    'Enterprise mobile app (iOS & Android)',
                    'Premium custom design',
                    'Full backend development',
                    'Advanced integrations',
                    'E-commerce ready',
                    'Real-time features',
                    'Advanced security',
                    'Premium SEO & analytics',
                    'CMS integration',
                    '6 months support',
                    'App store optimization',
                    'Priority support',
                    'Dedicated account manager'
                ],
                'is_popular' => false,
                'order' => 9,
                'is_active' => true,
            ],
        ];

        // Arabic Pricing Plans
        $arabicPlans = [
            // Website Development Plans
            [
                'language' => 'ar',
                'service_type' => 'website',
                'tier' => 'basic',
                'name' => 'موقع أساسي',
                'slug' => 'basic-website-ar',
                'description' => 'مثالي للشركات الصغيرة والناشئة',
                'price_monthly' => 299,
                'price_yearly' => 2990,
                'features' => [
                    'حتى 5 صفحات',
                    'تصميم متجاوب',
                    'تحسين SEO أساسي',
                    'نموذج اتصال',
                    'دعم لمدة شهر',
                    'شهادة SSL'
                ],
                'is_popular' => false,
                'order' => 1,
                'is_active' => true,
            ],
            [
                'language' => 'ar',
                'service_type' => 'website',
                'tier' => 'professional',
                'name' => 'موقع احترافي',
                'slug' => 'professional-website-ar',
                'description' => 'مثالي للشركات المتنامية',
                'price_monthly' => 599,
                'price_yearly' => 5990,
                'features' => [
                    'حتى 15 صفحة',
                    'تصميم متجاوب متقدم',
                    'تحسين SEO متقدم',
                    'نماذج اتصال متعددة',
                    'تكامل المدونة',
                    'دعم لمدة 3 أشهر',
                    'شهادة SSL',
                    'Google Analytics'
                ],
                'is_popular' => true,
                'order' => 2,
                'is_active' => true,
            ],
            [
                'language' => 'ar',
                'service_type' => 'website',
                'tier' => 'enterprise',
                'name' => 'موقع مؤسسي',
                'slug' => 'enterprise-website-ar',
                'description' => 'للمؤسسات الكبيرة',
                'price_monthly' => 1299,
                'price_yearly' => 12990,
                'features' => [
                    'صفحات غير محدودة',
                    'تصميم مخصص',
                    'تحسين SEO متميز',
                    'نماذج وتكاملات متقدمة',
                    'تكامل نظام إدارة المحتوى',
                    'جاهز للتجارة الإلكترونية',
                    'دعم لمدة 6 أشهر',
                    'شهادة SSL',
                    'تحليلات متقدمة',
                    'دعم ذو أولوية'
                ],
                'is_popular' => false,
                'order' => 3,
                'is_active' => true,
            ],

            // Mobile App Development Plans
            [
                'language' => 'ar',
                'service_type' => 'app',
                'tier' => 'basic',
                'name' => 'تطبيق موبايل أساسي',
                'slug' => 'basic-mobile-app-ar',
                'description' => 'تطبيق بسيط لنظام iOS أو Android',
                'price_monthly' => 499,
                'price_yearly' => 4990,
                'features' => [
                    'منصة واحدة (iOS أو Android)',
                    'حتى 5 شاشات',
                    'تصميم UI/UX أساسي',
                    'إشعارات فورية',
                    'تحليلات أساسية',
                    'دعم لمدة شهر',
                    'نشر في متجر التطبيقات'
                ],
                'is_popular' => false,
                'order' => 4,
                'is_active' => true,
            ],
            [
                'language' => 'ar',
                'service_type' => 'app',
                'tier' => 'professional',
                'name' => 'تطبيق موبايل احترافي',
                'slug' => 'professional-mobile-app-ar',
                'description' => 'تطبيق متعدد المنصات بمميزات كاملة',
                'price_monthly' => 999,
                'price_yearly' => 9990,
                'features' => [
                    'متعدد المنصات (iOS و Android)',
                    'حتى 15 شاشة',
                    'تصميم UI/UX مخصص',
                    'إشعارات فورية',
                    'مشتريات داخل التطبيق',
                    'تكامل API',
                    'تحليلات متقدمة',
                    'دعم لمدة 3 أشهر',
                    'نشر في متجر التطبيقات'
                ],
                'is_popular' => true,
                'order' => 5,
                'is_active' => true,
            ],
            [
                'language' => 'ar',
                'service_type' => 'app',
                'tier' => 'enterprise',
                'name' => 'تطبيق موبايل مؤسسي',
                'slug' => 'enterprise-mobile-app-ar',
                'description' => 'حل موبايل على مستوى المؤسسات',
                'price_monthly' => 2499,
                'price_yearly' => 24990,
                'features' => [
                    'متعدد المنصات (iOS و Android)',
                    'شاشات غير محدودة',
                    'تصميم مخصص متميز',
                    'مميزات متقدمة',
                    'تطوير الواجهة الخلفية',
                    'تكاملات طرف ثالث',
                    'مميزات الوقت الفعلي',
                    'أمان متقدم',
                    'تحليلات متميزة',
                    'دعم لمدة 6 أشهر',
                    'تحسين متجر التطبيقات',
                    'دعم ذو أولوية'
                ],
                'is_popular' => false,
                'order' => 6,
                'is_active' => true,
            ],

            // Website + App Package Plans
            [
                'language' => 'ar',
                'service_type' => 'both',
                'tier' => 'basic',
                'name' => 'باقة أساسية',
                'slug' => 'basic-package-ar',
                'description' => 'موقع + تطبيق موبايل',
                'price_monthly' => 699,
                'price_yearly' => 6990,
                'features' => [
                    'موقع أساسي (5 صفحات)',
                    'تطبيق موبايل أساسي (منصة واحدة)',
                    'تصميم متجاوب',
                    'SEO أساسي',
                    'إشعارات فورية',
                    'نماذج اتصال',
                    'دعم لمدة شهر',
                    'شهادة SSL',
                    'نشر في متجر التطبيقات'
                ],
                'is_popular' => false,
                'order' => 7,
                'is_active' => true,
            ],
            [
                'language' => 'ar',
                'service_type' => 'both',
                'tier' => 'professional',
                'name' => 'باقة احترافية',
                'slug' => 'professional-package-ar',
                'description' => 'حضور رقمي كامل',
                'price_monthly' => 1399,
                'price_yearly' => 13990,
                'features' => [
                    'موقع احترافي (15 صفحة)',
                    'تطبيق موبايل متعدد المنصات',
                    'تصميم مخصص',
                    'SEO متقدم',
                    'تكامل API',
                    'إشعارات فورية',
                    'مشتريات داخل التطبيق',
                    'تكامل المدونة',
                    'تحليلات متقدمة',
                    'دعم لمدة 3 أشهر',
                    'شهادة SSL',
                    'نشر في متجر التطبيقات'
                ],
                'is_popular' => true,
                'order' => 8,
                'is_active' => true,
            ],
            [
                'language' => 'ar',
                'service_type' => 'both',
                'tier' => 'enterprise',
                'name' => 'باقة مؤسسية',
                'slug' => 'enterprise-package-ar',
                'description' => 'حل مؤسسي كامل',
                'price_monthly' => 2999,
                'price_yearly' => 29990,
                'features' => [
                    'موقع مؤسسي (صفحات غير محدودة)',
                    'تطبيق موبايل مؤسسي (iOS و Android)',
                    'تصميم مخصص متميز',
                    'تطوير الواجهة الخلفية الكامل',
                    'تكاملات متقدمة',
                    'جاهز للتجارة الإلكترونية',
                    'مميزات الوقت الفعلي',
                    'أمان متقدم',
                    'SEO وتحليلات متميزة',
                    'تكامل نظام إدارة المحتوى',
                    'دعم لمدة 6 أشهر',
                    'تحسين متجر التطبيقات',
                    'دعم ذو أولوية',
                    'مدير حساب مخصص'
                ],
                'is_popular' => false,
                'order' => 9,
                'is_active' => true,
            ],
        ];

        // Insert all plans
        foreach (array_merge($englishPlans, $arabicPlans) as $plan) {
            PricingPlan::create($plan);
        }

        $this->command->info('Pricing plans seeded successfully!');
        $this->command->info('Total plans created: ' . PricingPlan::count());
    }
}
