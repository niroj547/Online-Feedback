<?php
require_once 'includes/auth_admin.php';

$error   = '';
$success = '';

// ── Helper: convert "HH:MM" or "HH:MM:SS" to total minutes ─────────────────
function time_to_minutes(string $t): int {
    $parts = explode(':', $t);
    if (count($parts) < 2) {
        return 0;
    }
    return (int)$parts[0] * 60 + (int)$parts[1];
}

// ── Add time slot ────────────────────────────────────────────────────────────
if (isset($_POST['add'])) {
    $entity_type = $_POST['entity_type'] ?? '';
    $entity_id   = intval($_POST['entity_id'] ?? 0);
    $day         = $_POST['day_of_week'] ?? '';
    $start       = $_POST['start_time'] ?? '';
    $end         = $_POST['end_time'] ?? '';

    $valid_types = ['lecturer', 'tutor'];
    $valid_days  = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    if (!in_array($entity_type, $valid_types, true) || $entity_id <= 0 ||
        !in_array($day, $valid_days, true) || empty($start) || empty($end)) {
        $error = 'All fields are required.';
    } elseif ($start >= $end) {
        $error = 'Start time must be before end time.';
    } else {
        $new_start_min = time_to_minutes($start);
        $new_end_min   = time_to_minutes($end);

        // Fetch existing slots for this entity on this day
        $stmt = $conn->prepare(
            "SELECT id, start_time, end_time FROM schedule
              WHERE entity_type = ? AND entity_id = ? AND day_of_week = ?"
        );
        $stmt->bind_param("sis", $entity_type, $entity_id, $day);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        $conflict = null;
        foreach ($existing as $slot) {
            $s_start = time_to_minutes(substr($slot['start_time'], 0, 5));
            $s_end   = time_to_minutes(substr($slot['end_time'],   0, 5));

            // No conflict only when there is at least a 30-min gap in both directions.
            // Conflict if: NOT (new_end + 30 <= s_start  OR  s_end + 30 <= new_start)
            if (!($new_end_min + 30 <= $s_start || $s_end + 30 <= $new_start_min)) {
                $conflict = $slot;
                break;
            }
        }

        if ($conflict !== null) {
            $cs = substr($conflict['start_time'], 0, 5);
            $ce = substr($conflict['end_time'],   0, 5);
            $error = "Conflicts with existing slot {$cs}–{$ce} on {$day}. "
                   . "A minimum 30-minute gap is required between any two slots.";
        } else {
            $stmt = $conn->prepare(
                "INSERT INTO schedule (entity_type, entity_id, day_of_week, start_time, end_time)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("sisss", $entity_type, $entity_id, $day, $start, $end);
            $stmt->execute();
            $stmt->close();
            $success = 'Time slot added successfully.';
        }
    }
}

// ── Delete time slot ─────────────────────────────────────────────────────────
if (isset($_POST['delete'])) {
    $id = intval($_POST['slot_id'] ?? 0);
    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM schedule WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $success = 'Time slot removed.';
    }
}

// ── Fetch data ────────────────────────────────────────────────────────────────
$filter_type = '';
if (in_array($_GET['type'] ?? '', ['lecturer', 'tutor'], true)) {
    $filter_type = $_GET['type'];
}

$day_order = "FIELD(s.day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')";

if ($filter_type !== '') {
    $stmt = $conn->prepare(
        "SELECT s.id, s.entity_type, s.entity_id, s.day_of_week,
                s.start_time, s.end_time,
                COALESCE(l.name, t.name) AS entity_name
           FROM schedule s
           LEFT JOIN lecturer l ON s.entity_type = 'lecturer' AND s.entity_id = l.id
           LEFT JOIN tutor    t ON s.entity_type = 'tutor'    AND s.entity_id = t.id
          WHERE s.entity_type = ?
          ORDER BY entity_name, {$day_order}, s.start_time"
    );
    $stmt->bind_param("s", $filter_type);
    $stmt->execute();
    $slots = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $slots = $conn->query(
        "SELECT s.id, s.entity_type, s.entity_id, s.day_of_week,
                s.start_time, s.end_time,
                COALESCE(l.name, t.name) AS entity_name
           FROM schedule s
           LEFT JOIN lecturer l ON s.entity_type = 'lecturer' AND s.entity_id = l.id
           LEFT JOIN tutor    t ON s.entity_type = 'tutor'    AND s.entity_id = t.id
          ORDER BY s.entity_type, entity_name, {$day_order}, s.start_time"
    )->fetch_all(MYSQLI_ASSOC);
}

$lecturers = $conn->query(
    "SELECT id, name FROM lecturer WHERE status = 'active' ORDER BY name"
)->fetch_all(MYSQLI_ASSOC);

$tutors = $conn->query(
    "SELECT id, name FROM tutor WHERE status = 'active' ORDER BY name"
)->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Schedules</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">
<div class="container">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Manage Schedules</h2>
    <a href="admin_dashboard.php" class="btn btn-secondary btn-sm">← Back to Dashboard</a>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <!-- ── Add new time slot ─────────────────────────────────────────────── -->
  <div class="card mb-4">
    <div class="card-header fw-semibold">Add Time Slot</div>
    <div class="card-body">
      <form method="post" class="row g-3" id="addSlotForm">
        <div class="col-md-2">
          <label class="form-label">Type</label>
          <select name="entity_type" id="entityType" class="form-select" required>
            <option value="">— select —</option>
            <option value="lecturer" <?= ($_POST['entity_type'] ?? '') === 'lecturer' ? 'selected' : '' ?>>Lecturer</option>
            <option value="tutor"    <?= ($_POST['entity_type'] ?? '') === 'tutor'    ? 'selected' : '' ?>>Tutor</option>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">Name</label>
          <select name="entity_id" id="entityId" class="form-select" required>
            <option value="">— select type first —</option>
          </select>
        </div>

        <div class="col-md-2">
          <label class="form-label">Day</label>
          <select name="day_of_week" class="form-select" required>
            <option value="">— select —</option>
            <?php foreach (['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $d): ?>
              <option value="<?= $d ?>" <?= ($_POST['day_of_week'] ?? '') === $d ? 'selected' : '' ?>><?= $d ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-2">
          <label class="form-label">Start Time</label>
          <input type="time" name="start_time" value="<?= htmlspecialchars($_POST['start_time'] ?? '') ?>"
                 class="form-control" required>
        </div>

        <div class="col-md-2">
          <label class="form-label">End Time</label>
          <input type="time" name="end_time" value="<?= htmlspecialchars($_POST['end_time'] ?? '') ?>"
                 class="form-control" required>
        </div>

        <div class="col-md-1 d-flex align-items-end">
          <button type="submit" name="add" class="btn btn-success w-100">Add</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ── Filter tabs ───────────────────────────────────────────────────── -->
  <ul class="nav nav-tabs mb-3">
    <li class="nav-item">
      <a class="nav-link <?= $filter_type === '' ? 'active' : '' ?>"
         href="admin_schedule.php">All</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $filter_type === 'lecturer' ? 'active' : '' ?>"
         href="admin_schedule.php?type=lecturer">Lecturers</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $filter_type === 'tutor' ? 'active' : '' ?>"
         href="admin_schedule.php?type=tutor">Tutors</a>
    </li>
  </ul>

  <!-- ── Slots table ───────────────────────────────────────────────────── -->
  <table class="table table-bordered table-hover bg-white">
    <thead class="table-dark">
      <tr>
        <th>Type</th>
        <th>Name</th>
        <th>Day</th>
        <th>Start</th>
        <th>End</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($slots)): ?>
        <tr><td colspan="6" class="text-center text-muted">No time slots added yet.</td></tr>
      <?php else: ?>
        <?php foreach ($slots as $slot): ?>
          <tr>
            <td><?= ucfirst(htmlspecialchars($slot['entity_type'])) ?></td>
            <td><?= htmlspecialchars($slot['entity_name'] ?? '—') ?></td>
            <td><?= htmlspecialchars($slot['day_of_week']) ?></td>
            <td><?= substr($slot['start_time'], 0, 5) ?></td>
            <td><?= substr($slot['end_time'],   0, 5) ?></td>
            <td>
              <form method="post" class="d-inline"
                    onsubmit="return confirm('Remove this time slot?');">
                <input type="hidden" name="slot_id" value="<?= (int)$slot['id'] ?>">
                <button name="delete" class="btn btn-danger btn-sm">Remove</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

</div>

<script>
// Populate entity name dropdown based on selected type
const lecturers = <?= json_encode(array_map(fn($l) => ['id' => $l['id'], 'name' => $l['name']], $lecturers)) ?>;
const tutors    = <?= json_encode(array_map(fn($t) => ['id' => $t['id'], 'name' => $t['name']], $tutors)) ?>;

const selectedEntityId = <?= json_encode(intval($_POST['entity_id'] ?? 0)) ?>;
const selectedType     = <?= json_encode($_POST['entity_type'] ?? '') ?>;

function populateEntityDropdown(type, preselect) {
    const sel  = document.getElementById('entityId');
    const data = type === 'lecturer' ? lecturers : type === 'tutor' ? tutors : [];
    sel.innerHTML = '<option value="">— select —</option>';
    data.forEach(item => {
        const opt = document.createElement('option');
        opt.value       = item.id;
        opt.textContent = item.name;
        if (item.id === preselect) opt.selected = true;
        sel.appendChild(opt);
    });
}

document.getElementById('entityType').addEventListener('change', function () {
    populateEntityDropdown(this.value, 0);
});

// Restore selection after a failed POST (page reload with error)
if (selectedType) {
    document.getElementById('entityType').value = selectedType;
    populateEntityDropdown(selectedType, selectedEntityId);
}

// Client-side: end time must be after start time
document.getElementById('addSlotForm').addEventListener('submit', function (e) {
    const start = this.querySelector('[name="start_time"]').value;
    const end   = this.querySelector('[name="end_time"]').value;
    if (start && end && start >= end) {
        e.preventDefault();
        alert('Start time must be before end time.');
    }
});
</script>
</body>
</html>
