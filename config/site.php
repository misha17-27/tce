<?php
/**
 * Единый источник данных сайта.
 * Меняйте тексты, телефоны, услуги и проекты только здесь — шаблоны трогать не нужно.
 */

return [

    // ── Общие данные ────────────────────────────────────────────────────────
    'name'        => 'TCE',
    'full_name'   => 'Turan Construction and Engineering',
    'tagline'     => 'Keyfiyyətli tikinti və mühəndislik həlləri',
    'lang'        => 'az',
    'base_url'    => '', // напр. '/tce-php' если сайт лежит в подпапке. Пусто = корень домена.

    // ── Контакты ────────────────────────────────────────────────────────────
    'contacts' => [
        'phone'      => '+994 77 505 33 99',
        'phone_href' => '+994775053399',
        'email'      => 'info@tce.az',
        'address'    => 'Əhməd Rəcəbli küç., Bakı, Azərbaycan',
        'map_embed'  => 'https://www.google.com/maps?q=Ahmad+Rajabli,+Baku&output=embed',
        'hours'      => 'B.e — Cümə, 09:00 – 18:00',
        'socials'    => [
            ['label' => 'Facebook',  'url' => '#'],
            ['label' => 'Instagram', 'url' => '#'],
            ['label' => 'LinkedIn',  'url' => '#'],
        ],
    ],

    // ── Навигация ───────────────────────────────────────────────────────────
    'nav' => [
        ''             => 'Ana səhifə',
        'haqqimizda'   => 'Haqqımızda',
        'xidmetlerimiz'=> 'Xidmətlərimiz',
        'layiheler'    => 'Layihələr',
        'elaqe'        => 'Əlaqə',
    ],

    // ── Цифры для счётчика ──────────────────────────────────────────────────
    'stats' => [
        ['value' => 10, 'suffix' => '+',  'label' => 'illik təcrübə'],
        ['value' => 120, 'suffix' => '+', 'label' => 'tamamlanmış layihə'],
        ['value' => 45, 'suffix' => '',   'label' => 'mühəndis və usta'],
        ['value' => 16, 'suffix' => '',   'label' => 'daimi tərəfdaş'],
    ],

    // ── Услуги ──────────────────────────────────────────────────────────────
    'services' => [
        [
            'slug'  => 'layihelendirme',
            'code'  => '01',
            'title' => 'Layihələndirmə',
            'lead'  => 'Konsepsiyadan işçi sənədlərinə qədər tam layihə paketi.',
            'text'  => 'Obyektin funksiyasını, yükünü və istismar şəraitini nəzərə alaraq memarlıq, konstruktiv və mühəndis bölmələrini hazırlayırıq. Hər bölmə dövlət standartlarına uyğun rəsmiləşdirilir və ekspertizaya təqdim olunmağa hazır verilir.',
            'items' => [
                'Memarlıq və planlaşdırma həlləri',
                'Konstruktiv hesabatlar',
                'İstilik, ventilyasiya və su təchizatı',
                'Elektrik və zəif cərəyan sistemləri',
                'Smeta və material hesabatı',
            ],
        ],
        [
            'slug'  => 'tikinti-qurasdirma',
            'code'  => '02',
            'title' => 'Tikinti və quraşdırma',
            'lead'  => 'Sıfırdan açar təslimi: bünövrədən son quraşdırmaya qədər.',
            'text'  => 'Öz texnika parkımız və daimi briqadalarımızla işi subpodratçıdan asılı olmadan aparırıq. Hər mərhələ üzrə icra sənədi tərtib olunur, gizli işlər aktla təsdiqlənir.',
            'items' => [
                'Torpaq və bünövrə işləri',
                'Monolit və metal konstruksiyalar',
                'Fasad və dam örtüyü',
                'Daxili tikinti və dekorasiya',
                'Sahə üzrə texniki nəzarət',
            ],
        ],
        [
            'slug'  => 'avadanliq-techizati',
            'code'  => '03',
            'title' => 'Avadanlıqların təchizatı',
            'lead'  => 'Sertifikatlı material və sənaye avadanlığının tədarükü.',
            'text'  => 'Birbaşa istehsalçı və rəsmi distribyutorlarla işləyirik. Tədarük qrafiki tikinti qrafikinə bağlanır ki, material sahəyə lazım olan gün çatsın, anbarda aylarla qalmasın.',
            'items' => [
                'Sənaye avadanlığı və nasos qrupları',
                'Metal və tikinti materialları',
                'Elektrik avadanlığı və kabel məhsulları',
                'Gömrük rəsmiləşdirilməsi və logistika',
                'Zəmanət sənədlərinin təhvili',
            ],
        ],
        [
            'slug'  => 'satis-sonrasi-xidmet',
            'code'  => '04',
            'title' => 'Satış sonrası xidmətlər',
            'lead'  => 'Obyekt təhvil verildikdən sonra da yanınızdayıq.',
            'text'  => 'Zəmanət müddəti ərzində baxış qrafiki üzrə işləyirik, sonra isə müqavilə əsasında texniki xidmət təklif edirik. Nasazlıq bildirişinə iş günü ərzində cavab verilir.',
            'items' => [
                'Zəmanət xidməti və dövri baxış',
                'Avadanlığın sazlanması',
                'Ehtiyat hissələrin təchizatı',
                'İstismar üzrə personalın təlimi',
                'Texniki dəstək xətti',
            ],
        ],
    ],

    // ── Преимущества ────────────────────────────────────────────────────────
    'advantages' => [
        ['title' => 'Təcrübə və peşəkarlıq', 'text' => 'Mühəndislik və tikinti sahəsində 10 ildən artıq praktiki iş təcrübəsi.'],
        ['title' => 'Kompleks həllər',        'text' => 'Layihələndirmədən texniki dəstəyə qədər bütün mərhələlər bir podratçıda.'],
        ['title' => 'Yüksək keyfiyyət',       'text' => 'Beynəlxalq standartlara uyğun material və müasir tikinti texnologiyaları.'],
        ['title' => 'Müştəri məmnuniyyəti',   'text' => 'Hər obyekt üçün ayrıca komanda və birbaşa əlaqə saxlanılan layihə rəhbəri.'],
        ['title' => 'İnnovativ yanaşma',      'text' => 'BIM modelləşdirmə və rəqəmsal nəzarət alətlərinin tətbiqi.'],
        ['title' => 'Qrafikə sadiqlik',       'text' => 'İş qrafiki müqaviləyə yazılır, gecikmə riskləri əvvəlcədən planlaşdırılır.'],
    ],

    // ── Этапы работы ────────────────────────────────────────────────────────
    'process' => [
        ['step' => 'Tanışlıq',        'text' => 'Sahəyə baxış, tapşırığın dəqiqləşdirilməsi və ilkin büdcə çərçivəsi.'],
        ['step' => 'Layihə',          'text' => 'Konsepsiya, hesabatlar və işçi sənədlərin hazırlanması.'],
        ['step' => 'Razılaşdırma',    'text' => 'Smeta, qrafik və müqavilə şərtlərinin təsdiqi.'],
        ['step' => 'İcra',            'text' => 'Tikinti-quraşdırma işləri və mərhələli təhvil.'],
        ['step' => 'Təhvil',          'text' => 'İcra sənədləri, zəmanət və istismara başlama.'],
    ],

    // ── Проекты (замените на свои) ──────────────────────────────────────────
    'projects' => [
        [
            'slug'     => 'villa-layihe',
            'title'    => 'Villa layihə',
            'category' => 'Yaşayış',
            'year'     => '2026',
            'location' => 'Bakı, Novxanı',
            'area'     => '480 m²',
            'client'   => 'Fərdi sifarişçi',
            'cover'    => 'assets/img/projects/villa.jpg',
            'gallery'  => ['assets/img/projects/villa.jpg'],
            'summary'  => 'İki mərtəbəli fərdi yaşayış evi: layihələndirmə, tikinti və daxili işlər.',
            'body'     => 'Sifarişçi ailəsi üçün iki mərtəbəli fərdi ev layihələndirilmiş və açar təslimi şəkildə təhvil verilmişdir. Konstruktiv həll kimi monolit karkas seçilib, fasadda təbii daş və şüşə kombinasiyası tətbiq olunub. Mühəndis sistemləri — istilik, ventilyasiya, su və elektrik — vahid layihə çərçivəsində icra edilib.',
            'scope'    => ['Layihələndirmə', 'Bünövrə və karkas', 'Fasad işləri', 'Mühəndis sistemləri', 'Daxili dekorasiya'],
        ],
        [
            'slug'     => 'the-glass-house',
            'title'    => 'The Glass House',
            'category' => 'Yaşayış',
            'year'     => '2025',
            'location' => 'Bakı',
            'area'     => '620 m²',
            'client'   => 'Fərdi sifarişçi',
            'cover'    => 'assets/img/projects/glass-house.jpg',
            'gallery'  => ['assets/img/projects/glass-house.jpg'],
            'summary'  => 'Panoram şüşələnmiş fasad və açıq planlı yaşayış həlli.',
            'body'     => 'Layihənin əsas tələbi maksimum təbii işıq idi. Bunun üçün daşıyıcı yükü daxili karkasa keçirilib, fasad isə enerji-effektiv ikiqat şüşə paketlərlə həll edilib. İstilik itkisini azaltmaq üçün fasad qovşaqları ayrıca hesablanıb.',
            'scope'    => ['Konstruktiv hesabat', 'Şüşə fasad sistemi', 'İstilik-ventilyasiya', 'Tikinti-quraşdırma'],
        ],
        [
            'slug'     => 'the-xs-luxury-apartment',
            'title'    => 'The XS Luxury Apartment',
            'category' => 'İnteryer',
            'year'     => '2025',
            'location' => 'Bakı, Nəsimi',
            'area'     => '145 m²',
            'client'   => 'Fərdi sifarişçi',
            'cover'    => 'assets/img/projects/xs-apartment.jpg',
            'gallery'  => ['assets/img/projects/xs-apartment.jpg'],
            'summary'  => 'Mənzilin tam yenidən planlaşdırılması və premium səviyyədə təmiri.',
            'body'     => 'Mövcud mənzildə daşıyıcı olmayan arakəsmələr sökülüb, funksional zonalar yenidən qurulub. Elektrik və santexnika şəbəkələri tam dəyişdirilib, bütün gizli işlər sənədləşdirilib.',
            'scope'    => ['Yenidən planlaşdırma', 'Elektrik və santexnika', 'Daxili dekorasiya', 'Mebel quraşdırılması'],
        ],
        [
            'slug'     => 'the-pool-house',
            'title'    => 'The Pool House',
            'category' => 'Kommersiya',
            'year'     => '2025',
            'location' => 'Abşeron',
            'area'     => '310 m²',
            'client'   => 'Özəl şirkət',
            'cover'    => 'assets/img/projects/pool-house.jpg',
            'gallery'  => ['assets/img/projects/pool-house.jpg'],
            'summary'  => 'Hovuz kompleksi: su hazırlığı sistemi və ətraf infrastruktur.',
            'body'     => 'Hovuz çanağı, texniki otaq və su hazırlığı sistemi vahid layihə kimi icra edilib. Filtrasiya və istilik avadanlığı seçilərkən illik istismar xərci hesablanaraq sifarişçiyə təqdim olunub.',
            'scope'    => ['Layihələndirmə', 'Hovuz konstruksiyası', 'Su hazırlığı sistemi', 'Ətraf abadlıq'],
        ],
    ],

    // ── Партнёры (положите логотипы в assets/img/partners/) ─────────────────
    'partners' => [
        ['name' => 'Partner 01', 'logo' => 'assets/img/partners/p01.png'],
        ['name' => 'Partner 02', 'logo' => 'assets/img/partners/p02.png'],
        ['name' => 'Partner 03', 'logo' => 'assets/img/partners/p03.png'],
        ['name' => 'Partner 04', 'logo' => 'assets/img/partners/p04.png'],
        ['name' => 'Partner 05', 'logo' => 'assets/img/partners/p05.png'],
        ['name' => 'Partner 06', 'logo' => 'assets/img/partners/p06.png'],
        ['name' => 'Partner 07', 'logo' => 'assets/img/partners/p07.png'],
        ['name' => 'Partner 08', 'logo' => 'assets/img/partners/p08.png'],
    ],

    // ── Форма обратной связи ────────────────────────────────────────────────
    'mail' => [
        'to'      => 'info@tce.az',
        'subject' => 'Saytdan yeni müraciət',
        // false = письма не отправляются, заявки пишутся в storage/messages.log
        'enabled' => false,
    ],
];
