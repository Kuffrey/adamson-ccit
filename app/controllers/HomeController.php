<?php
declare(strict_types=1);

class HomeController {
    // Ensure Model base class is loaded first
    private function requireModels() {
        require_once __DIR__ . '/../models/Model.php';
        require_once __DIR__ . '/../models/HomepageSettings.php';
        require_once __DIR__ . '/../models/Program.php';
        require_once __DIR__ . '/../models/Partner.php';
        require_once __DIR__ . '/../models/News.php';
        require_once __DIR__ . '/../models/Event.php';
    }
    public function index(): string {
        $this->requireModels();
        // HERO, WHY, SPOTLIGHT, CTA (from HomepageSettings)
        $settings = (new HomepageSettings())->get();
        $hero = [
            'eyebrow' => $settings['hero_eyebrow'] ?? '',
            'title' => $settings['hero_title'] ?? '',
            'subtitle' => $settings['hero_subtitle'] ?? '',
            'actions' => [
                [
                    'label' => $settings['btn_primary_text'] ?? '',
                    'url' => $settings['btn_primary_url'] ?? '',
                    'class' => 'btn--solid'
                ],
                [
                    'label' => $settings['btn_secondary_text'] ?? '',
                    'url' => $settings['btn_secondary_url'] ?? '',
                    'class' => 'btn--ghost'
                ]
            ]
        ];

        // QUICK ACTIONS (hardcoded for now, can be made dynamic)
        $quickActions = [
            [
                'label' => 'Admissions',
                'icon' => '<svg viewBox="0 0 24 24" width="22" height="22"><path fill="currentColor" d="M5 4h14a1 1 0 0 1 1 1v13l-3-2-3 2-3-2-3 2-3-2V5a1 1 0 0 1 1-1z"/></svg>',
                'url' => '/adamson-ccit/public/index.php?page=admission_requirements'
            ],
            [
                'label' => 'Programs',
                'icon' => '<svg viewBox="0 0 24 24" width="22" height="22"><path fill="currentColor" d="M12 3 1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>',
                'url' => '/adamson-ccit/public/index.php?page=programs_undergraduate'
            ],
            [
                'label' => 'Scholarships',
                'icon' => '<svg viewBox="0 0 24 24" width="22" height="22"><path fill="currentColor" d="M12 2a7 7 0 1 1-4.95 2.05A7 7 0 0 1 12 2zm-1 8h2v6h-2zm0 8h2v2h-2z"/></svg>',
                'url' => '/adamson-ccit/public/index.php?page=student_scholarships'
            ],
            [
                'label' => 'Student Life',
                'icon' => '<svg viewBox="0 0 24 24" width="22" height="22"><path fill="currentColor" d="M12 2a5 5 0 1 1-5 5 5 5 0 0 1 5-5Zm8 18v-2H4v-2a6 6 0 0 1 8-5.29A6 6 0 0 1 20 20Z"/></svg>',
                'url' => '/adamson-ccit/public/index.php?page=student_organizations'
            ],
            [
                'label' => 'Faculty',
                'icon' => '<svg viewBox="0 0 24 24" width="22" height="22"><path fill="currentColor" d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm-7 9v-2a7 7 0 0 1 14 0v2Z"/></svg>',
                'url' => '/adamson-ccit/public/index.php?page=faculty_profile'
            ],
            [
                'label' => 'News',
                'icon' => '<svg viewBox="0 0 24 24" width="22" height="22"><path fill="currentColor" d="M4 4h16v2H4zm0 4h10v2H4zm0 4h16v2H4zm0 4h10v2H4z"/></svg>',
                'url' => '/adamson-ccit/public/index.php?page=news'
            ]
        ];

        // WHY CHOOSE (fully dynamic from settings)
        $why = [
            'title' => $settings['why_title'] ?? '',
            'subtitle' => $settings['why_subtitle'] ?? '',
            'cards' => [
                [
                    'icon' => '<svg viewBox="0 0 24 24" width="28" height="28"><path fill="currentColor" d="M12 3a5 5 0 0 0-5 5v1a5 5 0 0 0 10 0V8a5 5 0 0 0-5-5Zm0 12c-5 0-9 3-9 6v2h18v-2c0-3-4-6-9-6Z"/></svg>',
                    'title' => $settings['why_faculty_text'] ?? '',
                    'description' => $settings['why_faculty_desc'] ?? '',
                    'link_label' => $settings['why_faculty_link_label'] ?? '',
                    'link_url' => $settings['why_faculty_link'] ?? ''
                ],
                [
                    'icon' => '<svg viewBox="0 0 24 24" width="28" height="28"><path fill="currentColor" d="M3 5h18v12H3z"/><path fill="currentColor" d="M2 19h20v2H2z"/></svg>',
                    'title' => $settings['why_facilities_text'] ?? '',
                    'description' => $settings['why_facilities_desc'] ?? '',
                    'link_label' => $settings['why_facilities_link_label'] ?? '',
                    'link_url' => $settings['why_facilities_link'] ?? ''
                ],
                [
                    'icon' => '<svg viewBox="0 0 24 24" width="28" height="28"><path fill="currentColor" d="M10 2h4l1 3h4a1 1 0 0 1 1 1v13a3 3 0 0 1-3 3H7a3 3 0 0 1-3-3V6a1 1 0 0 1 1-1h4l1-3zM7 10h10v2H7v-2z"/></svg>',
                    'title' => $settings['why_career_text'] ?? '',
                    'description' => $settings['why_career_desc'] ?? '',
                    'link_label' => $settings['why_career_link_label'] ?? '',
                    'link_url' => $settings['why_career_link'] ?? ''
                ]
            ]
        ];

        // SPOTLIGHT (fully dynamic from settings)
        $spotlight = [
            'eyebrow' => $settings['spotlight_eyebrow'] ?? '',
            'title' => $settings['spotlight_title'] ?? '',
            'description' => $settings['spotlight_blurb'] ?? '',
            'image' => $settings['spotlight_image'] ?? '',
            'image_alt' => $settings['spotlight_image_alt'] ?? '',
            'video_url' => $settings['spotlight_video_url'] ?? '',
            'actions' => [
                [
                    'label' => $settings['spotlight_cta_text'] ?? '',
                    'url' => $settings['spotlight_cta_url'] ?? '',
                    'class' => 'btn--solid'
                ],
                [
                    'label' => $settings['spotlight_cta2_text'] ?? '',
                    'url' => $settings['spotlight_cta2_url'] ?? '',
                    'class' => 'btn--outline-blue'
                ]
            ]
        ];

        // PROGRAMS
        $programs = [
            'title' => 'Explore Our Programs',
            'subtitle' => 'Find the path that fits your goals—from undergraduate to graduate studies.',
            'list' => array_map(function($p) {
                return [
                    'title' => $p['title'] ?? $p['name'] ?? '',
                    'description' => $p['description'] ?? '',
                    'image' => $p['image'] ?? $p['image_url'] ?? '/adamson-ccit/public/assets/images/programs/undergrad.jpg',
                    'image_alt' => $p['image_alt'] ?? '',
                    'url' => $p['url'] ?? '/adamson-ccit/public/index.php?page=programs_undergraduate'
                ];
            }, Program::all())
        ];

        // PARTNERS
        $partners = array_map(function($p) {
            return [
                'name' => $p['name'],
                'logo' => $p['logo_path'] ?? $p['logo'] ?? '',
                'description' => $p['short_desc'] ?? $p['description'] ?? '',
                'url' => $p['website_url'] ?? $p['url'] ?? '#'
            ];
        }, (new Partner())->listActive());

        // NEWS
        $newsList = array_slice(News::all(), 0, 3);
        $news = [
            'title' => 'News & Events',
            'view_all_url' => '/adamson-ccit/public/index.php?page=news',
            'articles' => array_map(function($n) {
                return [
                    'title' => $n['title'],
                    'summary' => $n['excerpt'] ?? '',
                    'image' => $n['image_url'] ?? '',
                    'chip' => ucfirst($n['category'] ?? 'News'),
                    'chip_class' => '',
                    'url' => '/adamson-ccit/public/index.php?page=news#' . $n['id']
                ];
            }, $newsList)
        ];

        // EVENTS
        $eventList = array_slice(Event::all(), 0, 3);
        $events = array_map(function($e) {
            $date = !empty($e['start_at']) ? new DateTime($e['start_at']) : null;
            return [
                'title' => $e['title'],
                'date' => $e['start_at'] ?? '',
                'day' => $date ? $date->format('d') : '',
                'month' => $date ? $date->format('M') : '',
                'details' => $e['location'] ?? '',
                'url' => '#'
            ];
        }, $eventList);

        // CTA (fully dynamic from settings)
        $cta = [
            'title' => $settings['cta_title'] ?? '',
            'description' => $settings['cta_description'] ?? '',
            'action_label' => $settings['cta_action_label'] ?? '',
            'action_url' => $settings['cta_action_url'] ?? ''
        ];

        // Make variables available to the view
        ob_start();
        include __DIR__ . '/../views/home.php';
        return ob_get_clean();
    }

    // NEW: public list of announcements
    public function announcements(): string {
        ob_start();
        include __DIR__ . '/../views/announcements.php';
        return ob_get_clean();
    }

    // NEW: public single announcement view
    public function announcementView(): string {
        ob_start();
        include __DIR__ . '/../views/announcement_view.php';
        return ob_get_clean();
    }
}
