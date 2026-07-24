<?php
/**
 * Builds a fresh, plain-text "knowledge base" of the business straight from
 * the database every time the AI is asked something. This means whatever
 * the admin edits in the dashboard (prices, services, courses, products...)
 * is what the AI knows about — nothing is hardcoded, nothing goes stale.
 */
function build_ai_context(mysqli $conn): string {
    $out = [];

    $out[] = "=== ABOUT DAN CREATIVES ===";
    $about = $conn->query("SELECT * FROM about_content LIMIT 1");
    if ($about && $about->num_rows) {
        $a = $about->fetch_assoc();
        if (!empty($a['instructor_name'])) {
            $out[] = "Founder/Instructor: " . $a['instructor_name'] . ($a['instructor_title'] ? " (" . $a['instructor_title'] . ")" : "");
        }
        if (!empty($a['instructor_bio'])) {
            $out[] = "About: " . $a['instructor_bio'];
        }
        if (!empty($a['channel_description'])) {
            $out[] = "YouTube channel: " . $a['channel_description'];
        }
    }

    $stats = $conn->query("SELECT stat_name, stat_value FROM site_stats");
    if ($stats && $stats->num_rows) {
        $parts = [];
        while ($s = $stats->fetch_assoc()) {
            $parts[] = $s['stat_value'] . " " . $s['stat_name'];
        }
        $out[] = "Track record: " . implode(", ", $parts);
    }

    $out[] = "\n=== SERVICES (design services, custom quotes) ===";
    $services = $conn->query("SELECT * FROM services WHERE status='active' ORDER BY display_order");
    if ($services) {
        while ($sv = $services->fetch_assoc()) {
            $features = str_replace('|', ', ', $sv['features']);
            $out[] = "- {$sv['title']}: {$sv['description']} Starting price: {$sv['price']}. Includes: {$features}.";

            $pkgs = $conn->query("SELECT * FROM service_packages WHERE service_id={$sv['id']} AND status='active' ORDER BY display_order");
            if ($pkgs && $pkgs->num_rows) {
                while ($p = $pkgs->fetch_assoc()) {
                    $pf = str_replace('|', ', ', $p['features']);
                    $out[] = "    Package \"{$p['package_name']}\" — {$p['package_price']}: {$pf}";
                }
            }
        }
    }

    $out[] = "\n=== PRINT-ON-DEMAND PRODUCTS (physical items) ===";
    $products = $conn->query("SELECT * FROM products WHERE status='active' ORDER BY display_order");
    if ($products) {
        while ($pr = $products->fetch_assoc()) {
            $out[] = "- {$pr['title']} ({$pr['category']}): {$pr['description']} Price: {$pr['price']}.";
        }
    }

    $out[] = "\n=== COURSES ===";
    $courses = $conn->query("SELECT * FROM courses");
    if ($courses) {
        while ($c = $courses->fetch_assoc()) {
            $status = $c['status'] === 'coming_soon' ? 'Coming soon' : 'Enrolling now';
            $badge = $c['badge_text'] ? " [{$c['badge_text']}]" : "";
            $out[] = "- {$c['title']}{$badge}: {$c['description']} Price: {$c['price']}. Duration: {$c['duration']}. Start date: {$c['start_date']}. Status: {$status}.";
        }
    }

    $out[] = "\n=== HOW ORDERING / BOOKING WORKS ===";
    $out[] = "- For design services: the client picks a service and package on the Services page and submits a request, or you (the assistant) can collect their name, phone, telegram username, and requirements and tell them the team will confirm on Telegram.";
    $out[] = "- For products: the client fills the order form on the Products page (name, phone, quantity).";
    $out[] = "- For courses: the client registers on the Courses page and uploads a payment receipt.";
    $out[] = "- Prices are in Ethiopian Birr (Birr). Payment and delivery details are confirmed by the team directly, not by you.";
    $out[] = "- The business is based in Ethiopia; most clients write in English or Amharic — always reply in the same language the visitor used.";

    return implode("\n", $out);
}
