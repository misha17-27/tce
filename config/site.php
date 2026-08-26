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
    'base_url'    => '/yeni', // напр. '/tce-php' если сайт лежит в подпапке. Пусто = корень домена.

    // ── Контакты ────────────────────────────────────────────────────────────
    'contacts' => [
        'phone'      => '+994 77 505 33 99',
        'phone_href' => '+994775053399',
        'email'      => 'info@tce.az',
        'address'    => 'Əhməd Rəcəbli küç., Bakı, Azərbaycan',
        'map_embed'  => 'https://www.google.com/maps?q=Ahmad+Rajabli,+Baku&output=embed',
        'hours'      => 'B.e — Cümə, 09:00 – 18:00',
        // Номер круглой WhatsApp-кнопки и иконки в подвале. Пусто — скрыто.
        'whatsapp'   => '+994 77 505 33 99',
        // Иконки в подвале; url «#» = иконка видна, но не кликается (пока нет ссылки).
        'socials'    => [
            ['label' => 'Instagram', 'url' => '#'],
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

    // ── Медиа ───────────────────────────────────────────────────────────────
    'media' => [
        'hero' => 'assets/img/hero.jpg',
    ],

    // ── Настройки (правятся в админке: SEO / SMTP / Безопасность) ───────────
    'settings' => [
        'og_image'        => '',
        'search_visible'  => '1',
        'notify_email'    => '',
        'turnstile_site'  => '',
        'turnstile_secret'=> '',
        'smtp_host'       => '',
        'smtp_port'       => '',
        'smtp_user'       => '',
        'smtp_pass'       => '',
        'smtp_secure'     => 'tls',
        'smtp_from'       => '',
        'smtp_from_name'  => '',
    ],

    // ── SEO ─────────────────────────────────────────────────────────────────
    // Title и description каждой страницы можно менять в админке → SEO.
    'seo' => [
        'description' => 'Turan Construction and Engineering (TCE) — layihələndirmə, tikinti-quraşdırma, avadanlıq təchizatı və satış sonrası xidmətlər. Bakı, Azərbaycan.',
        'robots'      => 'index,follow',
        'pages'       => [
            'home' => [
                'title'       => 'TCE — Tikinti və mühəndislik şirkəti | Bakı, Azərbaycan',
                'description' => 'Turan Construction and Engineering (TCE) — layihələndirmə, tikinti-quraşdırma, avadanlıq təchizatı və satış sonrası xidmətlər. Bakıda etibarlı tikinti tərəfdaşınız.',
            ],
            'haqqimizda' => [
                'title'       => 'Haqqımızda — TCE | Turan Construction and Engineering',
                'description' => 'TCE şirkəti haqqında: təcrübəli mühəndis komandası, müasir tikinti texnologiyaları və beynəlxalq standartlara uyğun keyfiyyət. Şirkətin dəyərləri və sertifikatları.',
            ],
            'xidmetlerimiz' => [
                'title'       => 'Xidmətlərimiz — tikinti və mühəndislik xidmətləri | TCE',
                'description' => 'Torpaq işləri, beton və metal konstruksiyalar, fasad işləri, mühəndis kommunikasiyaları, hidrotexniki işlər, yol tikintisi və sənaye obyektləri — TCE-nin tam xidmət spektri.',
            ],
            'layiheler' => [
                'title'       => 'Layihələr — tamamlanmış tikinti layihələri | TCE',
                'description' => 'TCE-nin tamamladığı layihələr: yaşayış evləri, kommersiya obyektləri və interyer işləri. Hər layihə üzrə foto, texniki göstəricilər və görülən işlərin təsviri.',
            ],
            'elaqe' => [
                'title'       => 'Əlaqə — TCE ilə əlaqə saxlayın | Bakı',
                'description' => 'TCE ilə əlaqə: +994 77 505 33 99, info@tce.az, Əhməd Rəcəbli küç., Bakı. Formu doldurun — bir iş günü ərzində cavab verib sahəyə ödənişsiz baxış təşkil edirik.',
            ],
            'layihe/villa-layihe' => [
                'title'       => 'Villa layihə — Novxanıda fərdi ev tikintisi | TCE',
                'description' => 'Novxanıda 480 m²-lik ikimərtəbəli villa: layihələndirmə, monolit karkas, təbii daş fasad, mühəndis sistemləri və daxili dekorasiya — açar təslimi TCE icrası.',
            ],
            'layihe/the-glass-house' => [
                'title'       => 'The Glass House — panoram şüşə fasadlı ev | TCE',
                'description' => 'Bakıda 620 m²-lik The Glass House layihəsi: enerji-effektiv ikiqat şüşə fasad, daxili karkas konstruksiyası və istilik-ventilyasiya sistemləri TCE tərəfindən.',
            ],
            'layihe/the-xs-luxury-apartment' => [
                'title'       => 'The XS Luxury Apartment — premium mənzil təmiri | TCE',
                'description' => 'Nəsimi rayonunda 145 m²-lik mənzilin tam yenidən planlaşdırılması və premium təmiri: elektrik, santexnika, daxili dekorasiya və mebel quraşdırılması — TCE.',
            ],
            'layihe/the-pool-house' => [
                'title'       => 'The Pool House — hovuz kompleksinin tikintisi | TCE',
                'description' => 'Abşeronda 310 m²-lik hovuz kompleksi: hovuz çanağı, su hazırlığı və filtrasiya sistemləri, texniki otaq və ətraf abadlıq — layihədən icraya TCE tərəfindən.',
            ],
        ],
    ],

    // ── Редактируемые тексты страниц (админка → Страницы) ───────────────────
    'content' => [
        'home.hero_title'       => 'Keyfiyyətli tikinti və mühəndislik həlləri ilə gələcəyinizi inşa edirik.',
        'home.intro_label'      => 'İnşa Etdiyimiz Həllər, Güclü Gələcəyin Zəminini Qoyur.',
        'home.intro_text'       => 'Azərbaycanın tikinti və mühəndislik sahəsində lider şirkətlərindən biri olaraq, biz layihələndirmə, avadanlıq təchizatı, tikinti və satış sonrası xidmətlər üzrə geniş təcrübəyə sahibik. Müştərilərimizə kompleks və innovativ həllər təqdim edərək, hər zaman yüksək keyfiyyət və peşəkarlıq təmin edirik. Hədəfimiz, hər bir layihəni mükəmməlliklə həyata keçirməkdir.',
        'home.projects_eyebrow' => 'Layihələr',
        'home.projects_title'   => 'Uğurlu layihələrimiz',
        'home.about_eyebrow'    => 'Haqqımızda',
        'home.about_lead'       => 'Azərbaycanın tikinti və mühəndislik sahəsində aparıcı şirkətlərindən biridir. Biz layihələndirmə, avadanlıq təchizatı, tikinti və satış sonrası xidmətlər təklif edirik.',
        'home.about_text'       => 'Müştərilərimizə kompleks həllər təqdim etməklə, yüksək keyfiyyət və peşəkarlıq vəd edirik. Hər bir layihəyə fərdi yanaşır, qrafik, büdcə və keyfiyyət nəzarətini vahid komandada saxlayırıq.',
        'home.adv_eyebrow'      => 'Üstünlüklər',
        'home.adv_title'        => 'Bizimlə işləmək üçün əsas səbəblər',
        'home.services_eyebrow' => 'Xidmətlər',
        'home.services_title'   => 'Dörd istiqamət, bir məsuliyyət',
        'home.partners_eyebrow' => 'Partnyorlar',
        'home.partners_title'   => 'Birlikdə işlədiyimiz şirkətlər',

        'about.kicker'     => 'Şirkət Haqqında',
        'about.intro'      => '13 yanvar 2025-ci ildə əsası qoyulan şirkətimiz inşaat və mühəndislik sahəsində innovativ və yüksək keyfiyyətli həllər təqdim edən etibarlı tərəfdaşdır. Biz müasir tikinti texnologiyalarından istifadə edərək iri infrastruktur layihələrinin, sənaye obyektlərinin, yaşayış və qeyri-yaşayış binalarının, hidrotexniki və yol tikinti işlərinin həyata keçirilməsində ixtisaslaşırıq.',
        'about.p1'         => 'Məqsədimiz müştərilərimizin ehtiyaclarına uyğun ən optimal və effektiv həlləri təqdim etməkdir. Bizim üçün hər bir layihə fərdi yanaşma tələb edir və biz yüksək peşəkarlıq, innovativ texnologiyalar və davamlı inkişaf prinsipləri əsasında fəaliyyət göstəririk.',
        'about.p2'         => 'Şirkətimizin peşəkar komandası mühəndislik və tikinti sahəsində geniş təcrübəyə malikdir. Təhlükəsizlik standartlarına ciddi riayət etməklə və ətraf mühitə dost texnologiyalar tətbiq etməklə, biz tikinti sahəsində dayanıqlı və uzunömürlü obyektlər yaradırıq.',
        'about.p3'         => 'Bizim fəaliyyət sahəmiz genişdir və torpaq işlərindən başlayaraq, yol və körpülərin tikintisinə, sənaye obyektlərinin inşasına qədər bir çox sahəni əhatə edir. Hər bir layihəmiz müasir standartlara cavab verir və keyfiyyətə zəmanət veririk.',
        'about.p4'         => 'Biz etibarlılığımız, dəqiqliyimiz və müştəri məmnuniyyətinə verdiyimiz üstünlük ilə seçilirik. Gələcəkdə daha böyük layihələr həyata keçirmək və tikinti sahəsində yeni nailiyyətlər əldə etmək üçün fəaliyyətimizi genişləndirməyə davam edirik.',
        'about.figcaption' => 'Keyfiyyətli tikinti və mühəndislik həlləri ilə gələcəyinizi inşa edirik.',
        'about.certs_title'=> 'Sertifikatlar',
        'about.image'      => 'assets/img/about/years.png',
        'about.cert1'      => 'assets/img/about/cert-01.webp',
        'about.cert2'      => 'assets/img/about/cert-02.webp',
        'about.cert3'      => 'assets/img/about/cert-03.webp',

        'services.kicker'       => 'Şirkət Haqqında',
        'services.hero_text'    => 'Biz tikinti və mühəndislik sahəsində geniş xidmətlər təqdim edirik. Yol və körpülərin tikintisi, sənaye obyektlərinin inşası, torpaq işləri və digər mühəndislik həlləri sahəsində peşəkar yanaşma ilə fəaliyyət göstəririk. Təhlükəsizlik standartlarına riayət edərək və innovativ texnologiyalardan istifadə edərək, uzunömürlü və dayanıqlı obyektlər inşa edirik.',
        'services.head_eyebrow' => 'Fəaliyyət istiqamətləri',
        'services.head_title'   => 'Tikinti və mühəndislik xidmətləri',

        'projects.lead' => 'Sənaye, infrastruktur və yaşayış obyektləri üzrə tamamlanmış işlərimiz.',

        'contact.lead'       => 'Zəng edin, yazın və ya formanı doldurun — bir iş günü ərzində cavab veririk.',
        'contact.info_title' => 'Rekvizitlər',
        'contact.form_title' => 'Müraciət formu',

        'cta.title' => 'Layihəniz var? Danışaq.',
        'cta.text'  => 'Sahəyə baxış və ilkin büdcə hesabatı ödənişsizdir. Bir iş günü ərzində cavab veririk.',
    ],

    // ── Группы услуг (страница «Xidmətlərimiz») ─────────────────────────────
    'service_groups' => [
        [
            'slug'  => 'xususi-torpaq-isleri',
            'title' => 'Xüsusi torpaq işləri',
            'items' => [
                'Ankerlərin torpağa bərkidilməsi və binaların “torpaqda divar” üsulu ilə tikilməsi.',
                'Enmə quyularının və kessonların qurulması.',
                'Qazma-partlayış işləri və qaya torpaqlarının çıxarılması.',
            ],
        ],
        [
            'slug'  => 'beton-metal-konstruksiyalar',
            'title' => 'Beton və metal konstruksiyalar',
            'items' => [
                'Beton və dəmir-beton konstruksiyaların quraşdırılması.',
                'Metal konstruksiyaların quraşdırılması.',
                'Taxta konstruksiyaların quraşdırılması.',
            ],
        ],
        [
            'slug'  => 'fasad-isleri',
            'title' => 'Fasad işləri',
            'items' => [
                'Binaların fasadlarının tikintisi və bərpası.',
            ],
        ],
        [
            'slug'  => 'muhendis-kommunikasiya-sebekeler',
            'title' => 'Mühəndis-kommunikasiya və şəbəkə sistemləri',
            'items' => [
                'Su, qaz, elektrik və rabitə şəbəkələrinin qurulması.',
            ],
        ],
        [
            'slug'  => 'hidrotekniki-isler',
            'title' => 'Hidrotekniki işlər',
            'items' => [
                'Sualtı-texniki işlər.',
                'Dambaların (torpaq bəndlərinin) tikintisi.',
                'Limanların, pirs və sahilbərkitmə sistemlərinin tikintisi.',
                'Su anbarlarının layihələndirilməsi və tikintisi.',
            ],
        ],
        [
            'slug'  => 'yol-tikintisi',
            'title' => 'Yol tikintisi',
            'items' => [
                'Avtomobil yollarının çəkilişi və yenidən qurulması.',
                'Aerodromların uçuş-enmə zolaqlarının tikintisi.',
                'Körpülər, estakadalar və yol ötürücülərinin tikintisi.',
            ],
        ],
        [
            'slug'  => 'xususi-qurgularin-tikintisi',
            'title' => 'Xüsusi qurğuların tikintisi',
            'items' => [
                'Qaz anbarları, rezervuarlar və qazholderlərin tikintisi.',
                'Elektrik ötürücü xətlərinin quraşdırılması.',
            ],
        ],
        [
            'slug'  => 'senaye-mulki-obyektler',
            'title' => 'Sənaye və mülki obyektlərin tikintisi',
            'items' => [
                'Aşırımı 24 metrədək və 24 metrdən çox olan tikililər.',
                'Hündürlüyü 65 metrədək və 65 metrdən çox olan obyektlər.',
                '5000 nəfərlik və daha çox tutumu olan binalar.',
            ],
        ],
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
            'video'    => 'assets/img/projects/villa/villa-video.mp4',
            'gallery'  => [
                'assets/img/projects/villa.jpg',
                'assets/img/projects/villa/villa-gallery-01.jpeg',
                'assets/img/projects/villa/villa-gallery-02.jpeg',
                'assets/img/projects/villa/villa-gallery-03.jpeg',
                'assets/img/projects/villa/villa-gallery-04.jpeg',
                'assets/img/projects/villa/villa-gallery-05.jpeg',
                'assets/img/projects/villa/villa-gallery-06.jpeg',
                'assets/img/projects/villa/villa-gallery-07.jpeg',
                'assets/img/projects/villa/villa-gallery-08.jpeg',
                'assets/img/projects/villa/villa-gallery-09.jpeg',
                'assets/img/projects/villa/villa-gallery-10.jpeg',
                'assets/img/projects/villa/villa-gallery-11.jpeg',
                'assets/img/projects/villa/villa-gallery-12.jpeg',
                'assets/img/projects/villa/villa-gallery-13.jpeg',
                'assets/img/projects/villa/villa-gallery-14.jpeg',
                'assets/img/projects/villa/villa-gallery-15.jpeg',
                'assets/img/projects/villa/villa-gallery-16.jpeg',
                'assets/img/projects/villa/villa-gallery-17.jpeg',
            ],
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

    // ── Партнёры (логотипы перенесены со старого сайта tce.az) ──────────────
    'partners' => [
        ['name' => 'Partner 01', 'logo' => 'assets/img/partners/partner-01.webp', 'sort' => 0,  'visible' => 1],
        ['name' => 'Partner 02', 'logo' => 'assets/img/partners/partner-02.webp', 'sort' => 1,  'visible' => 1],
        ['name' => 'Partner 03', 'logo' => 'assets/img/partners/partner-03.jpg',  'sort' => 2,  'visible' => 1],
        ['name' => 'Partner 04', 'logo' => 'assets/img/partners/partner-04.webp', 'sort' => 3,  'visible' => 1],
        ['name' => 'Partner 05', 'logo' => 'assets/img/partners/partner-05.png',  'sort' => 4,  'visible' => 1],
        ['name' => 'Partner 06', 'logo' => 'assets/img/partners/partner-06.webp', 'sort' => 5,  'visible' => 1],
        ['name' => 'Partner 07', 'logo' => 'assets/img/partners/partner-07.webp', 'sort' => 6,  'visible' => 1],
        ['name' => 'Partner 08', 'logo' => 'assets/img/partners/partner-08.png',  'sort' => 7,  'visible' => 1],
        ['name' => 'Partner 09', 'logo' => 'assets/img/partners/partner-09.png',  'sort' => 8,  'visible' => 1],
        ['name' => 'Partner 10', 'logo' => 'assets/img/partners/partner-10.png',  'sort' => 9,  'visible' => 1],
        ['name' => 'Partner 11', 'logo' => 'assets/img/partners/partner-11.png',  'sort' => 10, 'visible' => 1],
        ['name' => 'Partner 12', 'logo' => 'assets/img/partners/partner-12.webp', 'sort' => 11, 'visible' => 1],
        ['name' => 'Partner 13', 'logo' => 'assets/img/partners/partner-13.png',  'sort' => 12, 'visible' => 1],
        ['name' => 'Partner 14', 'logo' => 'assets/img/partners/partner-14.png',  'sort' => 13, 'visible' => 1],
        ['name' => 'Partner 15', 'logo' => 'assets/img/partners/partner-15.png',  'sort' => 14, 'visible' => 1],
        ['name' => 'Partner 16', 'logo' => 'assets/img/partners/partner-16.png',  'sort' => 15, 'visible' => 1],
    ],

    // ── Форма обратной связи ────────────────────────────────────────────────
    'mail' => [
        'to'      => 'info@tce.az',
        'subject' => 'Saytdan yeni müraciət',
        // false = письма не отправляются, заявки пишутся в storage/messages.log
        'enabled' => false,
    ],
];
