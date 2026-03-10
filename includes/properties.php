<?php
// includes/properties.php
// Centralized & reusable database queries for the entire site
// Compatible with MariaDB / XAMPP / MySQL
require_once 'config.php';

// ==================================================================
// INDEX.PHP QUERIES (already working perfectly)
// ==================================================================

function getLatestNews(int $limit = 2): array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, title, image, publish_date 
                           FROM news 
                           WHERE is_published = 1 
                           ORDER BY publish_date DESC 
                           LIMIT :limit");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getLeagueTable(): array {
    global $pdo;
    $sql = "
        SELECT c.id, c.name AS club, c.logo,
            COUNT(CASE WHEN ((f.home_club_id = c.id AND m.home_score > m.away_score) 
                          OR (f.away_club_id = c.id AND m.away_score > m.home_score)) THEN 1 END) AS wins,
            COUNT(CASE WHEN m.home_score = m.away_score THEN 1 END) AS draws,
            COUNT(CASE WHEN ((f.home_club_id = c.id AND m.home_score < m.away_score) 
                          OR (f.away_club_id = c.id AND m.away_score > m.home_score)) THEN 1 END) AS losses,
            (COUNT(CASE WHEN ((f.home_club_id = c.id AND m.home_score > m.away_score) 
                          OR (f.away_club_id = c.id AND m.away_score > m.home_score)) THEN 1 END) * 3 +
             COUNT(CASE WHEN m.home_score = m.away_score THEN 1 END)) AS points
        FROM clubs c
        LEFT JOIN fixtures f ON (f.home_club_id = c.id OR f.away_club_id = c.id)
        LEFT JOIN matches m ON m.fixture_id = f.id AND m.home_score IS NOT NULL
        GROUP BY c.id
        ORDER BY points DESC, wins DESC, club ASC
    ";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

function getUpcomingFixtures(int $limit = 5): array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT f.id, f.fixture_date, f.venue, 
                              f.home_club_id, f.away_club_id,
                              h.name AS home_name, h.logo AS home_logo, 
                              a.name AS away_name, a.logo AS away_logo
                           FROM fixtures f
                           JOIN clubs h ON f.home_club_id = h.id
                           JOIN clubs a ON f.away_club_id = a.id
                           WHERE f.status = 'Scheduled'
                           ORDER BY f.fixture_date ASC 
                           LIMIT :limit");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getLatestResults(int $limit = 5): array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT m.id AS match_id, m.home_score, m.away_score, m.match_date,
                            f.home_club_id, f.away_club_id,
                            h.name AS home_name, h.logo AS home_logo,
                            a.name AS away_name, a.logo AS away_logo
                         FROM matches m
                         JOIN fixtures f ON m.fixture_id = f.id
                         JOIN clubs h ON f.home_club_id = h.id
                         JOIN clubs a ON f.away_club_id = a.id
                         WHERE m.home_score IS NOT NULL
                         ORDER BY m.match_date DESC 
                         LIMIT :limit");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getMatchGoals(int $match_id): array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT g.minute, g.is_penalty, p.name AS scorer, p.club_id, 
                              ap.name AS assist
                           FROM goals g 
                           JOIN players p ON g.player_id = p.id
                           LEFT JOIN assists a ON g.id = a.goal_id
                           LEFT JOIN players ap ON a.player_id = ap.id
                           WHERE g.match_id = :mid
                           ORDER BY g.minute");
    $stmt->bindParam(':mid', $match_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getMatchCards(int $match_id): array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT c.card_type, c.minute, p.name, p.club_id 
                           FROM cards c 
                           JOIN players p ON c.player_id = p.id 
                           WHERE c.match_id = :mid");
    $stmt->bindParam(':mid', $match_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getRandomGalleryImages(int $limit = 6): array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT image FROM gallery ORDER BY RAND() LIMIT :limit");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ==================================================================
// CLUBS.PHP QUERIES
// ==================================================================

function getAllClubs(): array {
    global $pdo;
    return $pdo->query("SELECT * FROM clubs where name not in ('Loser 1','Loser 2','Winner 1','Winner 2') ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
}

function getClubById(int $club_id): ?array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM clubs WHERE id = :id");
    $stmt->bindParam(':id', $club_id, PDO::PARAM_INT);
    $stmt->execute();
    $club = $stmt->fetch(PDO::FETCH_ASSOC);
    return $club ?: null;
}

function getFullLeagueStandings(): array {
    global $pdo;
    $sql = "
        SELECT 
            c.id,
            COUNT(CASE WHEN ((f.home_club_id = c.id AND m.home_score > m.away_score) OR (f.away_club_id = c.id AND m.away_score > m.home_score)) THEN 1 END) AS wins,
            COUNT(CASE WHEN m.home_score = m.away_score THEN 1 END) AS draws,
            COUNT(CASE WHEN ((f.home_club_id = c.id AND m.home_score < m.away_score) OR (f.away_club_id = c.id AND m.away_score > m.home_score)) THEN 1 END) AS losses,
            COALESCE(SUM(CASE WHEN f.home_club_id = c.id THEN m.home_score ELSE m.away_score END), 0) AS gf,
            COALESCE(SUM(CASE WHEN f.home_club_id = c.id THEN m.away_score ELSE m.home_score END), 0) AS ga,
            (COUNT(CASE WHEN ((f.home_club_id = c.id AND m.home_score > m.away_score) OR (f.away_club_id = c.id AND m.away_score > m.home_score)) THEN 1 END) * 3 +
             COUNT(CASE WHEN m.home_score = m.away_score THEN 1 END)) AS points,
            (COALESCE(SUM(CASE WHEN f.home_club_id = c.id THEN m.home_score ELSE m.away_score END), 0) -
             COALESCE(SUM(CASE WHEN f.home_club_id = c.id THEN m.away_score ELSE m.home_score END), 0)) AS gd
        FROM clubs c
        LEFT JOIN fixtures f ON (f.home_club_id = c.id OR f.away_club_id = c.id)
        LEFT JOIN matches m ON m.fixture_id = f.id AND m.home_score IS NOT NULL
        GROUP BY c.id
        ORDER BY points DESC, gd DESC, gf DESC, c.name ASC
    ";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

function getClubRecentLeagueResults(int $club_id, int $limit = 10): array {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT 
            m.id AS match_id, m.home_score, m.away_score, m.match_date, f.matchday,
            f.home_club_id, f.away_club_id,
            h.name AS home_name, h.logo AS home_logo,
            a.name AS away_name, a.logo AS away_logo
        FROM matches m
        JOIN fixtures f ON m.fixture_id = f.id
        JOIN clubs h ON f.home_club_id = h.id
        JOIN clubs a ON f.away_club_id = a.id
        WHERE (f.home_club_id = :id OR f.away_club_id = :id) AND m.home_score IS NOT NULL
        ORDER BY m.match_date DESC LIMIT :limit
    ");
    $stmt->bindParam(':id', $club_id, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getClubUpcomingLeagueFixtures(int $club_id, int $limit = 10): array {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT 
            f.id, f.matchday, f.fixture_date, f.venue,
            f.home_club_id, f.away_club_id,
            h.name AS home_name, h.logo AS home_logo,
            a.name AS away_name, a.logo AS away_logo
        FROM fixtures f
        JOIN clubs h ON f.home_club_id = h.id
        JOIN clubs a ON f.away_club_id = a.id
        WHERE (f.home_club_id = :id OR f.away_club_id = :id) AND f.status = 'Scheduled'
        ORDER BY f.fixture_date ASC LIMIT :limit
    ");
    $stmt->bindParam(':id', $club_id, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getClubTournamentResults(int $club_id, int $limit = 10): array {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT 
            tm.id AS match_id, tm.home_score, tm.away_score, tm.match_date,
            tf.tournament_date, tf.venue,
            cs.name AS competition_name, cs.short_name,
            h.name AS home_name, h.logo AS home_logo,
            a.name AS away_name, a.logo AS away_logo,
            tf.home_club_id, tf.away_club_id
        FROM tournament_matches tm
        JOIN tournament_fixtures tf ON tm.fixture_id = tf.id
        JOIN competition_seasons cs ON tf.competition_season_id = cs.id
        JOIN clubs h ON tf.home_club_id = h.id
        JOIN clubs a ON tf.away_club_id = a.id
        WHERE (tf.home_club_id = :id OR tf.away_club_id = :id) AND tm.home_score IS NOT NULL
        ORDER BY tm.match_date DESC LIMIT :limit
    ");
    $stmt->bindParam(':id', $club_id, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getClubTournamentFixtures(int $club_id, int $limit = 10): array {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT 
            tf.id, tf.tournament_date, tf.venue,
            cs.name AS competition_name, cs.short_name,
            h.name AS home_name, h.logo AS home_logo,
            a.name AS away_name, a.logo AS away_logo,
            tf.home_club_id, tf.away_club_id
        FROM tournament_fixtures tf
        JOIN competition_seasons cs ON tf.competition_season_id = cs.id
        JOIN clubs h ON tf.home_club_id = h.id
        JOIN clubs a ON tf.away_club_id = a.id
        WHERE (tf.home_club_id = :id OR tf.away_club_id = :id) AND tf.status = 'Scheduled'
        ORDER BY tf.tournament_date ASC LIMIT :limit
    ");
    $stmt->bindParam(':id', $club_id, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getLeagueMatchEvents(int $match_id): array {
    global $pdo;

    // Goals
    $goalsStmt = $pdo->prepare("SELECT g.minute, g.is_penalty, p.name AS scorer, p.club_id, ap.name AS assist 
                                FROM goals g 
                                JOIN players p ON g.player_id = p.id 
                                LEFT JOIN assists a ON g.id = a.goal_id 
                                LEFT JOIN players ap ON a.player_id = ap.id 
                                WHERE g.match_id = ? ORDER BY g.minute");
    $goalsStmt->execute([$match_id]);
    $goals = $goalsStmt->fetchAll();

    // Cards
    $cardsStmt = $pdo->prepare("SELECT c.card_type, c.minute, p.name, p.club_id 
                                FROM cards c JOIN players p ON c.player_id = p.id WHERE c.match_id = ?");
    $cardsStmt->execute([$match_id]);
    $cards = $cardsStmt->fetchAll();

    // Clean Sheets
    $csStmt = $pdo->prepare("SELECT p.name, p.club_id FROM clean_sheets cs JOIN players p ON cs.player_id = p.id WHERE cs.match_id = ?");
    $csStmt->execute([$match_id]);
    $cleanSheets = $csStmt->fetchAll();

    return compact('goals', 'cards', 'cleanSheets');
}

function getTournamentMatchEvents(int $match_id): array {
    global $pdo;

    $tg = $pdo->prepare("SELECT tg.minute, tg.is_penalty, p.name AS scorer, p.club_id, ap.name AS assist 
                         FROM tournament_goals tg 
                         JOIN players p ON tg.player_id = p.id 
                         LEFT JOIN tournament_assists ta ON tg.id = ta.goal_id 
                         LEFT JOIN players ap ON ta.player_id = ap.id 
                         WHERE tg.match_id = ? ORDER BY tg.minute");
    $tg->execute([$match_id]);
    $goals = $tg->fetchAll();

    $tc = $pdo->prepare("SELECT tc.card_type, tc.minute, p.name, p.club_id 
                         FROM tournament_cards tc JOIN players p ON tc.player_id = p.id WHERE tc.match_id = ?");
    $tc->execute([$match_id]);
    $cards = $tc->fetchAll();

    $tcs = $pdo->prepare("SELECT p.name, p.club_id FROM tournament_clean_sheets tcs JOIN players p ON tcs.player_id = p.id WHERE tcs.match_id = ?");
    $tcs->execute([$match_id]);
    $cleanSheets = $tcs->fetchAll();

    return compact('goals', 'cards', 'cleanSheets');
}

// ==================================================================
// PLAYER STATS FOR CLUB – SEASON FILTERED (March → Dec) + G+A
// ==================================================================
function getClubPlayersWithStats(int $club_id, ?int $season_year = null): array {
    global $pdo;
    $season_year = $season_year ?? (int)($_GET['season'] ?? date('Y'));
    $season_start = "$season_year-03-01";
    $season_end = "$season_year-12-31";
    $sql = "
        SELECT
            p.id,
            p.name,
            p.photo,
            p.position,
            p.jersey_number,
            p.status,
            p.date_of_birth,
            TIMESTAMPDIFF(YEAR, p.date_of_birth, CURDATE()) AS age,
            -- Appearances
            (
                SELECT COUNT(DISTINCT m.id)
                FROM matches m
                JOIN fixtures f ON m.fixture_id = f.id
                WHERE (f.home_club_id = :club_id OR f.away_club_id = :club_id)
                  AND m.match_date BETWEEN :season_start1 AND :season_end1
                  AND m.home_score IS NOT NULL
            ) + (
                SELECT COUNT(DISTINCT tm.id)
                FROM tournament_matches tm
                JOIN tournament_fixtures tf ON tm.fixture_id = tf.id
                WHERE (tf.home_club_id = :club_id OR tf.away_club_id = :club_id)
                  AND tm.match_date BETWEEN :season_start2 AND :season_end2
                  AND tm.home_score IS NOT NULL
            ) AS appearances,
            -- Goals
            COALESCE((
                SELECT COUNT(*) FROM goals g
                JOIN matches m ON g.match_id = m.id
                WHERE g.player_id = p.id
                  AND m.match_date BETWEEN :season_start3 AND :season_end3
            ), 0) + COALESCE((
                SELECT COUNT(*) FROM tournament_goals tg
                JOIN tournament_matches tm ON tg.match_id = tm.id
                WHERE tg.player_id = p.id
                  AND tm.match_date BETWEEN :season_start4 AND :season_end4
            ), 0) AS goals,
            -- Assists
            COALESCE((
                SELECT COUNT(*) FROM assists a
                JOIN goals g ON a.goal_id = g.id
                JOIN matches m ON g.match_id = m.id
                WHERE a.player_id = p.id
                  AND m.match_date BETWEEN :season_start5 AND :season_end5
            ), 0) + COALESCE((
                SELECT COUNT(*) FROM tournament_assists ta
                JOIN tournament_goals tg ON ta.goal_id = tg.id
                JOIN tournament_matches tm ON tg.match_id = tm.id
                WHERE ta.player_id = p.id
                  AND tm.match_date BETWEEN :season_start6 AND :season_end6
            ), 0) AS assists,

            -- NEW: Goals + Assists (G/A)
            (COALESCE((
                SELECT COUNT(*) FROM goals g
                JOIN matches m ON g.match_id = m.id
                WHERE g.player_id = p.id
                  AND m.match_date BETWEEN :season_start3 AND :season_end3
            ), 0) + COALESCE((
                SELECT COUNT(*) FROM tournament_goals tg
                JOIN tournament_matches tm ON tg.match_id = tm.id
                WHERE tg.player_id = p.id
                  AND tm.match_date BETWEEN :season_start4 AND :season_end4
            ), 0)) + 
            (COALESCE((
                SELECT COUNT(*) FROM assists a
                JOIN goals g ON a.goal_id = g.id
                JOIN matches m ON g.match_id = m.id
                WHERE a.player_id = p.id
                  AND m.match_date BETWEEN :season_start5 AND :season_end5
            ), 0) + COALESCE((
                SELECT COUNT(*) FROM tournament_assists ta
                JOIN tournament_goals tg ON ta.goal_id = tg.id
                JOIN tournament_matches tm ON tg.match_id = tm.id
                WHERE ta.player_id = p.id
                  AND tm.match_date BETWEEN :season_start6 AND :season_end6
            ), 0)) AS ga,

            -- Yellow Cards
            COALESCE((
                SELECT COUNT(*) FROM cards c
                JOIN matches m ON c.match_id = m.id
                WHERE c.player_id = p.id AND c.card_type = 'yellow'
                  AND m.match_date BETWEEN :season_start7 AND :season_end7
            ), 0) + COALESCE((
                SELECT COUNT(*) FROM tournament_cards tc
                JOIN tournament_matches tm ON tc.match_id = tm.id
                WHERE tc.player_id = p.id AND tc.card_type = 'yellow'
                  AND tm.match_date BETWEEN :season_start8 AND :season_end8
            ), 0) AS yellow_cards,
            -- Red Cards
            COALESCE((
                SELECT COUNT(*) FROM cards c
                JOIN matches m ON c.match_id = m.id
                WHERE c.player_id = p.id AND c.card_type = 'red'
                  AND m.match_date BETWEEN :season_start9 AND :season_end9
            ), 0) + COALESCE((
                SELECT COUNT(*) FROM tournament_cards tc
                JOIN tournament_matches tm ON tc.match_id = tm.id
                WHERE tc.player_id = p.id AND tc.card_type = 'red'
                  AND tm.match_date BETWEEN :season_start10 AND :season_end10
            ), 0) AS red_cards,
            -- Clean Sheets (only for GKs)
            COALESCE((
                SELECT COUNT(*) FROM clean_sheets cs
                JOIN matches m ON cs.match_id = m.id
                WHERE cs.player_id = p.id
                  AND m.match_date BETWEEN :season_start11 AND :season_end11
            ), 0) + COALESCE((
                SELECT COUNT(*) FROM tournament_clean_sheets tcs
                JOIN tournament_matches tm ON tcs.match_id = tm.id
                WHERE tcs.player_id = p.id
                  AND tm.match_date BETWEEN :season_start12 AND :season_end12
            ), 0) AS clean_sheets
        FROM players p
        WHERE p.club_id = :club_id
        ORDER BY
            FIELD(p.position, 'GK', 'Goalkeeper') DESC,
            goals DESC,
            assists DESC,
            appearances DESC,
            p.jersey_number ASC,
            p.name ASC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':club_id' => $club_id,
        ':season_start1' => $season_start, ':season_end1' => $season_end,
        ':season_start2' => $season_start, ':season_end2' => $season_end,
        ':season_start3' => $season_start, ':season_end3' => $season_end,
        ':season_start4' => $season_start, ':season_end4' => $season_end,
        ':season_start5' => $season_start, ':season_end5' => $season_end,
        ':season_start6' => $season_start, ':season_end6' => $season_end,
        ':season_start7' => $season_start, ':season_end7' => $season_end,
        ':season_start8' => $season_start, ':season_end8' => $season_end,
        ':season_start9' => $season_start, ':season_end9' => $season_end,
        ':season_start10' => $season_start, ':season_end10' => $season_end,
        ':season_start11' => $season_start, ':season_end11' => $season_end,
        ':season_start12' => $season_start, ':season_end12' => $season_end,
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ==================================================================
// CONTACT_US.PHP QUERIES – FIXED & SAFE
// ==================================================================

function getSMTPSettings(): array {
    global $pdo;
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_key LIKE 'smtp_%'");
    $settings = [];
    foreach ($stmt->fetchAll(PDO::FETCH_KEY_PAIR) as $key => $value) {
        $settings[$key] = $value;
    }
    return $settings;
}

function getContactSocialLinks(): array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT platform_name, icon_class, url 
                           FROM social_links 
                           WHERE display_in_contact = 1 AND is_active = 1 
                           ORDER BY sort_order ASC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAdminEmailFromSocial(): string {
    global $pdo;
    $stmt = $pdo->prepare("SELECT url FROM social_links 
                           WHERE LOWER(platform_name) LIKE '%email%' 
                             AND display_in_contact = 1 
                           LIMIT 1");
    $stmt->execute();
    $email = $stmt->fetchColumn();
    return $email ? preg_replace('/^mailto:/i', '', $email) : 'info@ward24league.online';
}

// ==================================================================
// FIXTURES.PHP QUERIES
// ==================================================================

function getTotalMatchdays(): int {
    global $pdo;
    $total = $pdo->query("SELECT COALESCE(MAX(matchday), 38) FROM fixtures")->fetchColumn();
    return (int)$total;
}

function getAllUpcomingFixtures(): array {
    global $pdo;
    $stmt = $pdo->query("
        SELECT f.id, f.matchday, f.fixture_date, f.venue,
               f.home_club_id, f.away_club_id,
               h.name AS home_name, h.logo AS home_logo,
               a.name AS away_name, a.logo AS away_logo
        FROM fixtures f
        JOIN clubs h ON f.home_club_id = h.id
        JOIN clubs a ON f.away_club_id = a.id
        WHERE f.status = 'Scheduled'
        ORDER BY f.matchday ASC, f.fixture_date ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ==================================================================
// GALLERY.PHP QUERIES
// ==================================================================

function getAllGalleryImages(): array {
    global $pdo;
    $stmt = $pdo->query("
        SELECT image, title, description, uploaded_at 
        FROM gallery 
        ORDER BY uploaded_at DESC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ==================================================================
// LEAGUE.PHP – FULL LEAGUE STANDINGS
// ==================================================================

function getLeagueStandings(): array {
    global $pdo;

    $sql = "
        SELECT 
            c.id, 
            c.name AS club, 
            c.logo,
            COUNT(CASE WHEN ((f.home_club_id = c.id AND m.home_score > m.away_score) 
                          OR (f.away_club_id = c.id AND m.away_score > m.home_score)) THEN 1 END) AS wins,
            COUNT(CASE WHEN m.home_score = m.away_score THEN 1 END) AS draws,
            COUNT(CASE WHEN ((f.home_club_id = c.id AND m.home_score < m.away_score) 
                          OR (f.away_club_id = c.id AND m.away_score > m.home_score)) THEN 1 END) AS losses,
            COALESCE(SUM(CASE WHEN f.home_club_id = c.id THEN m.home_score ELSE m.away_score END), 0) AS goals_for,
            COALESCE(SUM(CASE WHEN f.home_club_id = c.id THEN m.away_score ELSE m.home_score END), 0) AS goals_against,
            (
                COUNT(CASE WHEN ((f.home_club_id = c.id AND m.home_score > m.away_score) 
                              OR (f.away_club_id = c.id AND m.away_score > m.home_score)) THEN 1 END) * 3 +
                COUNT(CASE WHEN m.home_score = m.away_score THEN 1 END)
            ) AS points,
            (
                COALESCE(SUM(CASE WHEN f.home_club_id = c.id THEN m.home_score ELSE m.away_score END), 0) -
                COALESCE(SUM(CASE WHEN f.home_club_id = c.id THEN m.away_score ELSE m.home_score END), 0)
            ) AS gd
        FROM clubs c
        LEFT JOIN fixtures f ON (f.home_club_id = c.id OR f.away_club_id = c.id)
        LEFT JOIN matches m ON m.fixture_id = f.id AND m.home_score IS NOT NULL
        where c.name not in ('Winner 1','Winner 2', 'Loser 1', 'Loser 2')
        GROUP BY c.id, c.name, c.logo
        ORDER BY 
            points DESC,
            gd DESC,
            goals_for DESC,
            c.name ASC
    ";

    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

function getLastFiveResults(int $clubId): array {
    global $pdo;

    $sql = "
        SELECT 
            CASE 
                WHEN f.home_club_id = :club_id THEN
                    CASE 
                        WHEN m.home_score > m.away_score THEN 'W'
                        WHEN m.home_score < m.away_score THEN 'L'
                        ELSE 'D'
                    END
                ELSE
                    CASE 
                        WHEN m.away_score > m.home_score THEN 'W'
                        WHEN m.away_score < m.home_score THEN 'L'
                        ELSE 'D'
                    END
            END AS result
        FROM fixtures f
        JOIN matches m ON m.fixture_id = f.id
        WHERE (f.home_club_id = :club_id OR f.away_club_id = :club_id)
          AND m.home_score IS NOT NULL
        ORDER BY m.match_date DESC, f.id DESC
        LIMIT 5
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':club_id' => $clubId]);
    $results = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

    return array_pad($results, 5, null);
}

function getCurrentLeagueSeason(): ?array {
    global $pdo;

    $sql = "SELECT name, season, short_name, competition_name 
            FROM competition_seasons 
            WHERE is_current = 1 AND type = 'league' 
            LIMIT 1";

    $stmt = $pdo->query($sql);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

// ==================================================================
// NEWS_ARTICLE.PHP QUERIES – FIXED FOR MARIADB / OLD MYSQL
// ==================================================================

function getPublishedArticleById(int $id): ?array {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT id, title, content, image, publish_date 
        FROM news 
        WHERE id = ? AND is_published = 1
        LIMIT 1
    ");
    $stmt->execute([$id]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);
    return $article ?: null;
}

function getOtherPublishedArticles(int $currentId, int $limit = 6): array {
    global $pdo;
    $sql = "
        SELECT id, title, image, publish_date 
        FROM news 
        WHERE is_published = 1 AND id <> ? 
        ORDER BY publish_date DESC 
        LIMIT " . (int)$limit;

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$currentId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ==================================================================
// PLAYER_PROFILE.PHP QUERIES
// ==================================================================

function getPlayerById(int $player_id): ?array {
    global $pdo;

    if ($player_id <= 0) return null;

    $stmt = $pdo->prepare("
        SELECT 
            p.id, p.name, p.photo, p.position, p.jersey_number,
            p.date_of_birth, p.nationality,
            c.name AS club_name, c.id AS club_id, c.logo AS club_logo
        FROM players p
        LEFT JOIN clubs c ON p.club_id = c.id
        WHERE p.id = ?
        LIMIT 1
    ");
    $stmt->execute([$player_id]);
    $player = $stmt->fetch(PDO::FETCH_ASSOC);

    return $player ?: null;
}

function getPlayerLeagueStats(int $player_id, ?int $season_id = null): array {   // ← FIXED
    global $pdo;

    $currentYear = date('Y');
    $startDate   = "$currentYear-01-01";
    $endDate     = "$currentYear-12-31";

    if (!$season_id) {
        $current = $pdo->query("SELECT id FROM competition_seasons WHERE is_current = 1 AND type = 'league' LIMIT 1")->fetch();
        $season_id = $current['id'] ?? null;
    }

    $stmt = $pdo->prepare("
        SELECT 
            COUNT(DISTINCT g.id) AS goals,
            COUNT(DISTINCT a.id) AS assists,
            SUM(CASE WHEN cards.card_type = 'yellow' THEN 1 ELSE 0 END) AS yellow_cards,
            SUM(CASE WHEN cards.card_type = 'red' THEN 1 ELSE 0 END) AS red_cards,
            COUNT(DISTINCT cs.id) AS clean_sheets
        FROM players p
        LEFT JOIN goals g ON g.player_id = p.id 
            AND g.match_id IN (
                SELECT m.id FROM matches m 
                JOIN fixtures f ON m.fixture_id = f.id 
                WHERE f.fixture_date BETWEEN ? AND ?
                AND (? IS NULL OR f.competition_season_id = ?)
            )
        LEFT JOIN assists a ON a.player_id = p.id 
            AND a.goal_id IN (
                SELECT g2.id FROM goals g2 
                JOIN matches m2 ON g2.match_id = m2.id 
                JOIN fixtures f2 ON m2.fixture_id = f2.id 
                WHERE f2.fixture_date BETWEEN ? AND ?
                AND (? IS NULL OR f2.competition_season_id = ?)
            )
        LEFT JOIN cards ON cards.player_id = p.id 
            AND cards.match_id IN (
                SELECT m.id FROM matches m 
                JOIN fixtures f ON m.fixture_id = f.id 
                WHERE f.fixture_date BETWEEN ? AND ?
                AND (? IS NULL OR f.competition_season_id = ?)
            )
        LEFT JOIN clean_sheets cs ON cs.player_id = p.id 
            AND cs.match_id IN (
                SELECT m.id FROM matches m 
                JOIN fixtures f ON m.fixture_id = f.id 
                WHERE f.fixture_date BETWEEN ? AND ?
                AND (? IS NULL OR f.competition_season_id = ?)
            )
        WHERE p.id = ?
    ");

    $stmt->execute([
        $startDate, $endDate, $season_id, $season_id,
        $startDate, $endDate, $season_id, $season_id,
        $startDate, $endDate, $season_id, $season_id,
        $startDate, $endDate, $season_id, $season_id,
        $player_id
    ]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return [
        'goals'         => (int)($result['goals'] ?? 0),
        'assists'       => (int)($result['assists'] ?? 0),
        'yellow_cards'  => (int)($result['yellow_cards'] ?? 0),
        'red_cards'     => (int)($result['red_cards'] ?? 0),
        'clean_sheets'  => (int)($result['clean_sheets'] ?? 0)
    ];
}

function getPlayerTournamentStatsByYear(int $player_id): array {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT 
            YEAR(tf.tournament_date) AS year,
            COUNT(DISTINCT tg.id) AS goals,
            COUNT(DISTINCT ta.goal_id) AS assists,
            SUM(CASE WHEN tc.card_type = 'yellow' THEN 1 ELSE 0 END) AS yellow_cards,
            SUM(CASE WHEN tc.card_type = 'red' THEN 1 ELSE 0 END) AS red_cards,
            COUNT(DISTINCT tcs.id) AS clean_sheets
        FROM tournament_fixtures tf
        LEFT JOIN tournament_matches tm ON tm.fixture_id = tf.id
        LEFT JOIN tournament_goals tg ON tg.match_id = tm.id AND tg.player_id = ?
        LEFT JOIN tournament_assists ta ON ta.player_id = ?
        LEFT JOIN tournament_cards tc ON tc.match_id = tm.id AND tc.player_id = ?
        LEFT JOIN tournament_clean_sheets tcs ON tcs.match_id = tm.id AND tcs.player_id = ?
        WHERE tf.tournament_date IS NOT NULL
        GROUP BY YEAR(tf.tournament_date)
        HAVING goals + assists + yellow_cards + red_cards + clean_sheets > 0
        ORDER BY year DESC
    ");

    $stmt->execute([$player_id, $player_id, $player_id, $player_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ==================================================================
// PLAYER_STATS.PHP QUERIES
// ==================================================================

function getAvailableSeasons(): array {
    global $pdo;

    $stmt = $pdo->query("
        (SELECT DISTINCT YEAR(fixture_date) AS year FROM fixtures WHERE fixture_date IS NOT NULL)
        UNION
        (SELECT DISTINCT YEAR(tournament_date) AS year FROM tournament_fixtures WHERE tournament_date IS NOT NULL)
        ORDER BY year DESC
    ");

    $years = $stmt->fetchAll(PDO::FETCH_COLUMN);
    return $years ?: [date('Y')];
}

function getTournaments(): array {
    global $pdo;

    $stmt = $pdo->query("
        SELECT id, name AS display_name, season
        FROM competition_seasons 
        WHERE type = 'tournament'
        ORDER BY season DESC, name ASC
    ");

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ==================================================================
// RESULTS.PHP – UPDATED FUNCTIONS
// ==================================================================

function getResultsTotalMatchdays(?int $year = null): int {   // ← FIXED
    global $pdo;
    $sql = "SELECT COALESCE(MAX(f.matchday), 38) FROM fixtures f";
    if ($year) {
        $sql = "
            SELECT COALESCE(MAX(f.matchday), 38) 
            FROM fixtures f 
            JOIN matches m ON f.id = m.fixture_id 
            WHERE YEAR(m.match_date) = ?
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$year]);
        return (int)$stmt->fetchColumn();
    }
    return (int)$pdo->query("SELECT COALESCE(MAX(matchday), 38) FROM fixtures")->fetchColumn();
}

function getAllCompletedMatchesWithEvents(?int $year = null): array {   // ← FIXED
    global $pdo;

    $sql = "
        SELECT 
            m.id AS match_id, m.home_score, m.away_score, m.match_date,
            f.matchday, f.home_club_id, f.away_club_id,
            h.name AS home_name, h.logo AS home_logo,
            a.name AS away_name, a.logo AS away_logo
        FROM matches m
        JOIN fixtures f ON m.fixture_id = f.id
        JOIN clubs h ON f.home_club_id = h.id
        JOIN clubs a ON f.away_club_id = a.id
        WHERE m.home_score IS NOT NULL AND m.away_score IS NOT NULL
    ";

    $params = [];
    if ($year !== null) {
        $sql .= " AND YEAR(m.match_date) = ?";
        $params[] = $year;
    }

    $sql .= " ORDER BY f.matchday DESC, m.match_date DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as &$r) {
        $matchId = $r['match_id'];

        // Goals + Assists
        $goalsStmt = $pdo->prepare("
            SELECT g.minute, g.is_penalty, p.name AS scorer, p.club_id,
                   ap.name AS assist
            FROM goals g
            JOIN players p ON g.player_id = p.id
            LEFT JOIN assists a ON g.id = a.goal_id
            LEFT JOIN players ap ON a.player_id = ap.id
            WHERE g.match_id = ?
            ORDER BY g.minute
        ");
        $goalsStmt->execute([$matchId]);
        $goals = $goalsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Cards
        $cardsStmt = $pdo->prepare("
            SELECT c.card_type, c.minute, p.name, p.club_id
            FROM cards c
            JOIN players p ON c.player_id = p.id
            WHERE c.match_id = ?
            ORDER BY c.minute
        ");
        $cardsStmt->execute([$matchId]);
        $cards = $cardsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Clean Sheets
        $csStmt = $pdo->prepare("
            SELECT p.name, p.club_id
            FROM clean_sheets cs
            JOIN players p ON cs.player_id = p.id
            WHERE cs.match_id = ?
        ");
        $csStmt->execute([$matchId]);
        $cleanSheets = $csStmt->fetchAll(PDO::FETCH_ASSOC);

        $events = [];
        foreach ($goals as $g) {
            $isHome = $g['club_id'] == $r['home_club_id'];
            $side = $isHome ? 'start' : 'end';
            $penalty = $g['is_penalty'] ? ' (P' : '';
            $assist = $g['assist'] ? " ← {$g['assist']}" : '';
            $events[] = "<div class='text-$side'><strong>{$g['scorer']}</strong> {$g['minute']}'$penalty$assist</div>";
        }
        foreach ($cards as $c) {
            $isHome = $c['club_id'] == $r['home_club_id'];
            $side = $isHome ? 'start' : 'end';
            $color = $c['card_type'] == 'yellow' ? 'warning' : 'danger';
            $events[] = "<div class='text-$side'><span class='text-$color'>●</span> <strong>{$c['name']}</strong> {$c['minute']}'</div>";
        }
        foreach ($cleanSheets as $cs) {
            $isHome = $cs['club_id'] == $r['home_club_id'];
            $side = $isHome ? 'start' : 'end';
            $events[] = "<div class='text-$side text-success fw-bold'>Clean Sheet → {$cs['name']}</div>";
        }

        $r['event_html'] = !empty($events)
            ? implode('', $events)
            : "<div class='text-center text-muted small'>No events recorded</div>";
    }
    unset($r);

    return $results;
}

// ==================================================================
// SEARCH_RESULTS.PHP QUERIES – FIXED (only named parameters)
// ==================================================================

function searchPlayers(string $query, int $limit = 30): array {
    global $pdo;

    $like = '%' . $query . '%';

    $stmt = $pdo->prepare("
        SELECT id, name 
        FROM players 
        WHERE name LIKE :like 
        ORDER BY name 
        LIMIT :limit
    ");

    $stmt->bindParam(':like',  $like,  PDO::PARAM_STR);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function searchClubs(string $query, int $limit = 30): array {
    global $pdo;

    $like = '%' . $query . '%';

    $stmt = $pdo->prepare("
        SELECT id, name 
        FROM clubs 
        WHERE name LIKE :like 
        ORDER BY name 
        LIMIT :limit
    ");

    $stmt->bindParam(':like',  $like,  PDO::PARAM_STR);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ==================================================================
// TOURNAMENTS.PHP – CENTRALIZED QUERIES (FINAL & COMPLETE)
// ==================================================================

function getUpcomingTournamentFixtures(int $limit = 6): array {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT f.id, f.tournament_date, f.venue,
               h.id AS home_club_id, h.name AS home_name, h.logo AS home_logo,
               a.id AS away_club_id, a.name AS away_name, a.logo AS away_logo
        FROM tournament_fixtures f
        JOIN clubs h ON f.home_club_id = h.id
        JOIN clubs a ON f.away_club_id = a.id
        WHERE f.status = 'Scheduled'
        ORDER BY f.tournament_date ASC
        LIMIT :limit
    ");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getRecentTournamentResults(int $limit = 6): array {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT m.id AS match_id, m.home_score, m.away_score, m.match_date,
               f.venue,
               h.id AS home_club_id, h.name AS home_name, h.logo AS home_logo,
               a.id AS away_club_id, a.name AS away_name, a.logo AS away_logo
        FROM tournament_matches m
        JOIN tournament_fixtures f ON m.fixture_id = f.id
        JOIN clubs h ON f.home_club_id = h.id
        JOIN clubs a ON f.away_club_id = a.id
        WHERE m.home_score IS NOT NULL
        ORDER BY m.match_date DESC
        LIMIT :limit
    ");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTournamentTopScorers(int $limit = 6): array {
    global $pdo;

    $sql = "
        SELECT 
            p.id, 
            p.name, 
            p.photo, 
            c.name AS club_name,
            COUNT(tg.id) AS tournament_goals
        FROM players p
        JOIN clubs c ON p.club_id = c.id
        LEFT JOIN tournament_goals tg ON tg.player_id = p.id
        GROUP BY p.id, p.name, p.photo, c.name
        HAVING COUNT(tg.id) > 0
        ORDER BY tournament_goals DESC, p.name ASC
        LIMIT :limit
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function renderTournamentMatchEventHtml(int $match_id, int $home_club_id, int $away_club_id, int $home_score, int $away_score): string {
    global $pdo;

    $events = [];

    // Goals + Assists
    $stmt = $pdo->prepare("
        SELECT g.minute, g.is_penalty, p.name AS player_name, p.club_id,
               ap.name AS assist_name
        FROM tournament_goals g
        JOIN players p ON g.player_id = p.id
        LEFT JOIN tournament_assists a ON g.id = a.goal_id
        LEFT JOIN players ap ON a.player_id = ap.id
        WHERE g.match_id = ?
        ORDER BY g.minute
    ");
    $stmt->execute([$match_id]);
    foreach ($stmt->fetchAll() as $g) {
        $side    = ($g['club_id'] == $home_club_id) ? 'text-start' : 'text-end';
        $penalty = $g['is_penalty'] ? ' (P)' : '';
        $assist  = $g['assist_name'] ? " ← " . htmlspecialchars($g['assist_name']) : '';
        $events[] = "<div class='$side'><strong>" . htmlspecialchars($g['player_name']) . "</strong> {$g['minute']}'$penalty$assist</div>";
    }

    // Cards
    $stmt = $pdo->prepare("
        SELECT c.card_type, c.minute, p.name AS player_name, p.club_id
        FROM tournament_cards c
        JOIN players p ON c.player_id = p.id
        WHERE c.match_id = ?
        ORDER BY c.minute
    ");
    $stmt->execute([$match_id]);
    foreach ($stmt->fetchAll() as $c) {
        $side  = ($c['club_id'] == $home_club_id) ? 'text-start' : 'text-end';
        $color = $c['card_type'] === 'yellow' ? 'warning' : 'danger';
        $events[] = "<div class='$side'><span class='text-$color'>●</span> <strong>" . htmlspecialchars($c['player_name']) . "</strong> {$c['minute']}'</div>";
    }

    // Clean Sheets
    if ($away_score == 0) {
        $stmt = $pdo->prepare("SELECT p.name FROM tournament_clean_sheets cs JOIN players p ON cs.player_id = p.id WHERE cs.match_id = ? AND p.club_id = ?");
        $stmt->execute([$match_id, $home_club_id]);
        foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $name) {
            $events[] = "<div class='text-start text-success fw-bold'>Clean Sheet → " . htmlspecialchars($name) . "</div>";
        }
    }
    if ($home_score == 0) {
        $stmt = $pdo->prepare("SELECT p.name FROM tournament_clean_sheets cs JOIN players p ON cs.player_id = p.id WHERE cs.match_id = ? AND p.club_id = ?");
        $stmt->execute([$match_id, $away_club_id]);
        foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $name) {
            $events[] = "<div class='text-end text-success fw-bold'>" . htmlspecialchars($name) . " ← Clean Sheet</div>";
        }
    }

    return !empty($events)
        ? implode('', $events)
        : '<div class="text-center text-muted small">No events recorded.</div>';
}