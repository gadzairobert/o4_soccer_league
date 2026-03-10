<?php
if (!defined('IN_DASHBOARD')) exit('Direct access not allowed');

// Only loads form fields when $match is set
if (empty($match)) return;

$fixture_id = $match['id'];

// Load existing result
$stmt = $pdo->prepare("SELECT home_goals, away_goals FROM fixtures WHERE id = ?");
$stmt->execute([$fixture_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// Load goals & cards
$events = $pdo->prepare("
    SELECT * FROM match_events 
    WHERE fixture_id = ? 
    ORDER BY minute ASC
")->execute([$fixture_id]) ? $pdo->query()->fetchAll(PDO::FETCH_ASSOC) : [];
?>

<div class="row mb-3">
    <div class="col-md-6">
        <label>Final Score - Home</label>
        <input type="number" name="home_goals" class="form-control" min="0" value="<?= $result['home_goals']??'' ?>" required>
    </div>
    <div class="col-md-6">
        <label>Final Score - Away</label>
        <input type="number" name="away_goals" class="form-control" min="0" value="<?= $result['away_goals']??'' ?>" required>
    </div>
</div>

<h5 class="mt-4">Goals & Cards</h5>
<div id="eventsContainer">
    <?php foreach ($events as $i => $e): ?>
        <div class="row mb-2 event-row" data-index="<?= $i ?>">
            <div class="col-md-3">
                <select name="events[<?= $i ?>][type]" class="form-select">
                    <option value="goal" <?= $e['event_type']=='goal'?'selected':'' ?>>Goal</option>
                    <option value="yellow" <?= $e['event_type']=='yellow'?'selected':'' ?>>Yellow Card</option>
                    <option value="red" <?= $e['event_type']=='red'?'selected':'' ?>>Red Card</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="number" name="events[<?= $i ?>][minute]" class="form-control" placeholder="Minute" value="<?= $e['minute'] ?>" min="1" max="120">
            </div>
            <div class="col-md-4">
                <input type="text" name="events[<?= $i ?>][player]" class="form-control" placeholder="Player Name" value="<?= htmlspecialchars($e['player_name']) ?>">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm remove-event">×</button>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<button type="button" class="btn btn-secondary btn-sm mt-2" id="addEvent">+ Add Event</button>

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'record_result'): ?>
<?php
    $fixture_id = (int)$_POST['fixture_id'];
    $home_goals = (int)$_POST['home_goals'];
    $away_goals = (int)$_POST['away_goals'];

    $pdo->beginTransaction();
    try {
        // Update score
        $pdo->prepare("UPDATE fixtures SET home_goals=?, away_goals=? WHERE id=?")
            ->execute([$home_goals, $away_goals, $fixture_id]);

        // Clear old events
        $pdo->prepare("DELETE FROM match_events WHERE fixture_id = ?")->execute([$fixture_id]);

        // Insert new events
        $stmt = $pdo->prepare("INSERT INTO match_events (fixture_id, event_type, minute, player_name) VALUES (?,?,?,?)");
        foreach ($_POST['events'] ?? [] as $e) {
            if (!empty($e['minute']) && !empty($e['player'])) {
                $stmt->execute([$fixture_id, $e['type'], (int)$e['minute'], trim($e['player'])]);
            }
        }

        $pdo->commit();
        $success = 'Match result saved.';
        // Reload page to avoid resubmit
        header("Location: ?page=fixtures&record_id=$fixture_id");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = 'Failed to save result: ' . $e->getMessage();
    }
?>
<?php endif; ?>

<script>
    let eventIndex = <?= count($events) ?>;
    document.getElementById('addEvent').addEventListener('click', () => {
        const html = `
        <div class="row mb-2 event-row" data-index="${eventIndex}">
            <div class="col-md-3">
                <select name="events[${eventIndex}][type]" class="form-select">
                    <option value="goal">Goal</option>
                    <option value="yellow">Yellow Card</option>
                    <option value="red">Red Card</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="number" name="events[${eventIndex}][minute]" class="form-control" placeholder="Minute" min="1" max="120">
            </div>
            <div class="col-md-4">
                <input type="text" name="events[${eventIndex}][player]" class="form-control" placeholder="Player Name">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm remove-event">×</button>
            </div>
        </div>`;
        document.getElementById('eventsContainer').insertAdjacentHTML('beforeend', html);
        eventIndex++;
    });
    document.addEventListener('click', e => {
        if (e.target.classList.contains('remove-event')) {
            e.target.closest('.event-row').remove();
        }
    });
</script>