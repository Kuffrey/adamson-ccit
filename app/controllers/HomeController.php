<?php
declare(strict_types=1);

final class HomeController
{
    /* ---------- Bootstrapping ---------- */
    private function requireModels(): void
    {
        require_once __DIR__ . '/../models/Model.php';
        require_once __DIR__ . '/../models/HomepageSettings.php';
        // Optional data models used on the homepage:
        require_once __DIR__ . '/../models/Program.php';
        require_once __DIR__ . '/../models/Partner.php';
        require_once __DIR__ . '/../models/News.php';
        require_once __DIR__ . '/../models/Event.php';
        // If you later add Announcements, require it here.
        // require_once __DIR__ . '/../models/Announcement.php';
    }

    /* ---------- Section builders (small + focused) ---------- */

    private function buildHero(array $s): array
    {
        return [
            'eyebrow'  => $s['hero_eyebrow']  ?? '',
            'title'    => $s['hero_title']    ?? '',
            'subtitle' => $s['hero_subtitle'] ?? '',
            'bg'       => $s['hero_bg']       ?? '', // note: home.php can be updated to use this
            'actions'  => array_values(array_filter([
                (!empty($s['btn_primary_text']) || !empty($s['btn_primary_url'])) ? [
                    'label' => $s['btn_primary_text'] ?? '',
                    'url'   => $s['btn_primary_url']  ?? '',
                    'class' => 'btn--solid',
                ] : null,
                (!empty($s['btn_secondary_text']) || !empty($s['btn_secondary_url'])) ? [
                    'label' => $s['btn_secondary_text'] ?? '',
                    'url'   => $s['btn_secondary_url']  ?? '',
                    'class' => 'btn--ghost',
                ] : null,
            ])),
        ];
    }

    private function buildQuickActions(): array
    {
        // Kept static for now; trivial to back with a table later.
        return [
            [
                'label' => 'Admissions',
                'icon'  => '<svg viewBox="0 0 24 24" width="22" height="22"><path fill="currentColor" d="M5 4h14a1 1 0 0 1 1 1v13l-3-2-3 2-3-2-3 2-3-2V5a1 1 0 0 1 1-1z"/></svg>',
                'url'   => '/adamson-ccit/public/index.php?page=admission_requirements',
            ],
            [
                'label' => 'Programs',
                'icon'  => '<svg viewBox="0 0 24 24" width="22" height="22"><path fill="currentColor" d="M12 3 1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>',
                'url'   => '/adamson-ccit/public/index.php?page=programs_undergraduate',
            ],
            [
                'label' => 'Scholarships',
                'icon'  => '<svg viewBox="0 0 24 24" width="22" height="22"><path fill="currentColor" d="M12 2a7 7 0 1 1-4.95 2.05A7 7 0 0 1 12 2zm-1 8h2v6h-2zm0 8h2v2h-2z"/></svg>',
                'url'   => '/adamson-ccit/public/index.php?page=student_scholarships',
            ],
            [
                'label' => 'Student Life',
                'icon'  => '<svg viewBox="0 0 24 24" width="22" height="22"><path fill="currentColor" d="M12 2a5 5 0 1 1-5 5 5 5 0 0 1 5-5Zm8 18v-2H4v-2a6 6 0 0 1 8-5.29A6 6 0 0 1 20 20Z"/></svg>',
                'url'   => '/adamson-ccit/public/index.php?page=student_organizations',
            ],
            [
                'label' => 'Faculty',
                'icon'  => '<svg viewBox="0 0 24 24" width="22" height="22"><path fill="currentColor" d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm-7 9v-2a7 7 0 0 1 14 0v2Z"/></svg>',
                'url'   => '/adamson-ccit/public/index.php?page=faculty_profile',
            ],
            [
                'label' => 'News',
                'icon'  => '<svg viewBox="0 0 24 24" width="22" height="22"><path fill="currentColor" d="M4 4h16v2H4zm0 4h10v2H4zm0 4h16v2H4zm0 4h10v2H4z"/></svg>',
                'url'   => '/adamson-ccit/public/index.php?page=news',
            ],
        ];
    }

    private function buildWhy(array $s): array
    {
        return [
            'title'    => $s['why_title']    ?? '',
            'subtitle' => $s['why_subtitle'] ?? '',
            'cards'    => [
                [
                    'icon'        => '<svg viewBox="0 0 24 24" width="28" height="28"><path fill="currentColor" d="M12 3a5 5 0 0 0-5 5v1a5 5 0 0 0 10 0V8a5 5 0 0 0-5-5Zm0 12c-5 0-9 3-9 6v2h18v-2c0-3-4-6-9-6Z"/></svg>',
                    'title'       => $s['why_faculty_text'] ?? '',
                    'description' => $s['why_faculty_desc'] ?? '',
                    'link_label'  => $s['why_faculty_link_label'] ?? '',
                    'link_url'    => $s['why_faculty_link'] ?? '',
                ],
                [
                    'icon'        => '<svg viewBox="0 0 24 24" width="28" height="28"><path fill="currentColor" d="M3 5h18v12H3z"/><path fill="currentColor" d="M2 19h20v2H2z"/></svg>',
                    'title'       => $s['why_facilities_text'] ?? '',
                    'description' => $s['why_facilities_desc'] ?? '',
                    'link_label'  => $s['why_facilities_link_label'] ?? '',
                    'link_url'    => $s['why_facilities_link'] ?? '',
                ],
                [
                    'icon'        => '<svg viewBox="0 0 24 24" width="28" height="28"><path fill="currentColor" d="M10 2h4l1 3h4a1 1 0 0 1 1 1v13a3 3 0 0 1-3 3H7a3 3 0 0 1-3-3V6a1 1 0 0 1 1-1h4l1-3zM7 10h10v2H7v-2z"/></svg>',
                    'title'       => $s['why_career_text'] ?? '',
                    'description' => $s['why_career_desc'] ?? '',
                    'link_label'  => $s['why_career_link_label'] ?? '',
                    'link_url'    => $s['why_career_link'] ?? '',
                ],
            ],
        ];
    }

    private function buildSpotlight(array $s): array
    {
        return [
            'eyebrow'     => $s['spotlight_eyebrow']      ?? '',
            'title'       => $s['spotlight_title']        ?? '',
            'description' => $s['spotlight_blurb']        ?? '',
            'image'       => $s['spotlight_image']        ?? '',
            'image_alt'   => $s['spotlight_image_alt']    ?? '',
            'video_url'   => $s['spotlight_video_url']    ?? '',
            'actions'     => array_values(array_filter([
                (!empty($s['spotlight_cta_text']) || !empty($s['spotlight_cta_url'])) ? [
                    'label' => $s['spotlight_cta_text'] ?? '',
                    'url'   => $s['spotlight_cta_url']  ?? '',
                    'class' => 'btn--solid',
                ] : null,
                (!empty($s['spotlight_cta2_text']) || !empty($s['spotlight_cta2_url'])) ? [
                    'label' => $s['spotlight_cta2_text'] ?? '',
                    'url'   => $s['spotlight_cta2_url']  ?? '',
                    'class' => 'btn--outline-blue',
                ] : null,
            ])),
        ];
    }

    private function buildPrograms(): array
    {
        $rows = [];
        try {
            $rows = Program::all();
        } catch (\Throwable $e) {
            $rows = [];
        }

        return [
            'title'    => 'Explore Our Programs',
            'subtitle' => 'Find the path that fits your goals—from undergraduate to graduate studies.',
            'list'     => array_map(function ($p): array {
                return [
                    'title'       => $p['title'] ?? $p['name'] ?? '',
                    'description' => $p['description'] ?? '',
                    'image'       => $p['image'] ?? ($p['image_url'] ?? '/adamson-ccit/public/assets/images/programs/undergrad.jpg'),
                    'image_alt'   => $p['image_alt'] ?? '',
                    'url'         => $p['url'] ?? '/adamson-ccit/public/index.php?page=programs_undergraduate',
                ];
            }, $rows),
        ];
    }

    private function buildPartners(): array
    {
        $rows = [];
        try {
            $rows = (new Partner())->listActive();
        } catch (\Throwable $e) {
            $rows = [];
        }

        return array_map(function ($p): array {
            return [
                'name'        => $p['name'] ?? '',
                'logo'        => $p['logo_path'] ?? ($p['logo'] ?? ''),
                'description' => $p['short_desc'] ?? ($p['description'] ?? ''),
                'url'         => $p['website_url'] ?? ($p['url'] ?? '#'),
            ];
        }, $rows);
    }

    private function buildNews(): array
    {
        $list = [];
        try {
            $list = array_slice(News::all(), 0, 3);
        } catch (\Throwable $e) {
            $list = [];
        }

        return [
            'title'        => 'News & Events',
            'view_all_url' => '/adamson-ccit/public/index.php?page=news',
            'articles'     => array_map(function ($n): array {
                return [
                    'title'     => $n['title'] ?? '',
                    'summary'   => $n['excerpt'] ?? '',
                    'image'     => $n['image_url'] ?? '',
                    'chip'      => ucfirst($n['category'] ?? 'News'),
                    'chip_class'=> '',
                    'url'       => '/adamson-ccit/public/index.php?page=news#' . ($n['id'] ?? ''),
                ];
            }, $list),
        ];
    }

    private function buildEvents(): array
    {
        $list = [];
        try {
            $list = array_slice(Event::all(), 0, 3);
        } catch (\Throwable $e) {
            $list = [];
        }

        return array_map(function ($e): array {
            $dateStr = $e['start_at'] ?? null;
            $day = $month = '';
            if ($dateStr) {
                try {
                    $dt   = new \DateTime($dateStr);
                    $day  = $dt->format('d');
                    $month= $dt->format('M');
                } catch (\Throwable $t) {
                    // keep blanks
                }
            }
            return [
                'title'  => $e['title'] ?? '',
                'date'   => $dateStr ?? '',
                'day'    => $day,
                'month'  => $month,
                'details'=> $e['location'] ?? '',
                'url'    => '#',
            ];
        }, $list);
    }

    private function buildCTA(array $s): array
    {
        return [
            'title'        => $s['cta_title']        ?? '',
            'description'  => $s['cta_description']  ?? '',
            'action_label' => $s['cta_action_label'] ?? '',
            'action_url'   => $s['cta_action_url']   ?? '',
        ];
    }

    /* ---------- Controller actions ---------- */

    public function index(): string
    {
        $this->requireModels();

        // Load DB-backed settings once
        $settings = (new HomepageSettings())->get();

        // Compose page view-model
        $hero         = $this->buildHero($settings);
        $quickActions = $this->buildQuickActions();
        $why          = $this->buildWhy($settings);
        $spotlight    = $this->buildSpotlight($settings);
        $programs     = $this->buildPrograms();
        $partners     = $this->buildPartners();
        $news         = $this->buildNews();
        $events       = $this->buildEvents();
        $cta          = $this->buildCTA($settings);

        // If you add announcements and a toggle in settings, you can pass them here:
        // $announcements = (!empty($settings['show_pinned_announcements']))
        //     ? Announcement::pinnedPublic()
        //     : [];

        ob_start();
        include __DIR__ . '/../views/home.php';
        return ob_get_clean();
    }

    // Public endpoints (already in your version)
    public function announcements(): string
    {
        ob_start();
        include __DIR__ . '/../views/announcements.php';
        return ob_get_clean();
    }

    public function announcementView(): string
    {
        ob_start();
        include __DIR__ . '/../views/announcement_view.php';
        return ob_get_clean();
    }
}
