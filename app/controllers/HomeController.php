<?php
declare(strict_types=1);

final class HomeController
{
    /* ---------- Bootstrapping ---------- */
    private function requireModels(): void
    {
        require_once __DIR__ . '/../models/Model.php';
        require_once __DIR__ . '/../models/HomepageSettings.php';
        require_once __DIR__ . '/../models/QuickAction.php';
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
        // Now dynamic - fetch from database
        try {
            $actions = QuickAction::getAllActive();
            return array_map(function ($action): array {
                return [
                    'id'    => $action['id'],
                    'label' => $action['label'] ?? '',
                    'icon'  => $action['icon'] ?? '',
                    'url'   => $action['url'] ?? '',
                ];
            }, $actions);
        } catch (\Throwable $e) {
            // Fallback to empty array if database error
            return [];
        }
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
                    'image'       => $s['why_faculty_image'] ?? '',
                ],
                [
                    'icon'        => '<svg viewBox="0 0 24 24" width="28" height="28"><path fill="currentColor" d="M3 5h18v12H3z"/><path fill="currentColor" d="M2 19h20v2H2z"/></svg>',
                    'title'       => $s['why_facilities_text'] ?? '',
                    'description' => $s['why_facilities_desc'] ?? '',
                    'link_label'  => $s['why_facilities_link_label'] ?? '',
                    'link_url'    => $s['why_facilities_link'] ?? '',
                    'image'       => $s['why_facilities_image'] ?? '',
                ],
                [
                    'icon'        => '<svg viewBox="0 0 24 24" width="28" height="28"><path fill="currentColor" d="M10 2h4l1 3h4a1 1 0 0 1 1 1v13a3 3 0 0 1-3 3H7a3 3 0 0 1-3-3V6a1 1 0 0 1 1-1h4l1-3zM7 10h10v2H7v-2z"/></svg>',
                    'title'       => $s['why_career_text'] ?? '',
                    'description' => $s['why_career_desc'] ?? '',
                    'link_label'  => $s['why_career_link_label'] ?? '',
                    'link_url'    => $s['why_career_link'] ?? '',
                    'image'       => $s['why_career_image'] ?? '',
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
            // Use searchPublished instead of all() to only show published news
            if (method_exists(News::class, 'searchPublished')) {
                $result = News::searchPublished([], ['page' => 1, 'perPage' => 3]);
                $list = $result['items'] ?? [];
            } else {
                // Fallback: Filter out non-published news
                $list = array_values(array_filter(News::all(), function($item) {
                    return strtolower($item['status'] ?? '') === 'published';
                }));
                $list = array_slice($list, 0, 3);
            }
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
                    'url'       => '/adamson-ccit/public/index.php?page=news_article&id=' . ($n['id'] ?? ''),
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
                'id'        => $e['id'] ?? '',
                'title'     => $e['title'] ?? '',
                'date'      => $dateStr ?? '',
                'day'       => $day,
                'month'     => $month,
                'details'   => $e['location'] ?? '',
                'description' => $e['description'] ?? '',
                'start_time' => $e['start_time'] ?? '',
                'end_time'  => $e['end_time'] ?? '',
                'url'       => '#event-' . ($e['id'] ?? ''),
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
