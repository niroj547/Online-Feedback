<?php

/**
 * Returns a Bootstrap badge for the given status value.
 */
function status_badge(string $status): string {
    $class = $status === 'active' ? 'bg-success' : 'bg-warning';
    return '<span class="badge ' . $class . '">' . ucfirst(htmlspecialchars($status)) . '</span>';
}

/**
 * Returns a self-contained inline form with a suspend or activate button.
 * Used in pages where each action needs its own <form> (e.g. courses, students).
 */
function status_action_form(int $id, string $status): string {
    $safe_id = (int) $id;
    $hidden = '<input type="hidden" name="id" value="' . $safe_id . '">';
    if ($status === 'active') {
        return '<form method="post" class="d-inline">' . $hidden .
               '<button name="suspend" class="btn btn-warning btn-sm">Suspend</button></form>';
    }
    return '<form method="post" class="d-inline">' . $hidden .
           '<button name="activate" class="btn btn-success btn-sm">Activate</button></form>';
}

/**
 * Returns a suspend or activate button (without a form wrapper).
 * Used inside row-level forms where suspend/activate share the form with other actions
 * (e.g. lecturers, tutors).
 */
function status_action_button(string $status, string $extra_class = 'me-2'): string {
    $class_attr = 'btn btn-' . ($status === 'active' ? 'warning' : 'success') . ' btn-sm' .
                  ($extra_class !== '' ? ' ' . htmlspecialchars($extra_class) : '');
    $name = $status === 'active' ? 'suspend' : 'activate';
    $label = $status === 'active' ? 'Suspend' : 'Activate';
    return '<button name="' . $name . '" class="' . $class_attr . '">' . $label . '</button>';
}

/**
 * Updates the status column of the given table for the specified record ID.
 * Only operates on whitelisted tables to prevent SQL injection.
 */
function update_entity_status(mysqli $conn, string $table, int $id, string $status): void {
    $table_map = [
        'course'   => 'course',
        'lecturer' => 'lecturer',
        'tutor'    => 'tutor',
        'students' => 'students',
    ];
    if (!isset($table_map[$table]) || $id <= 0) {
        return;
    }
    $safe_table = $table_map[$table];
    $stmt = $conn->prepare("UPDATE `{$safe_table}` SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    $stmt->close();
}

/**
 * Returns true if a course with the given ID exists in the database.
 */
function course_exists(mysqli $conn, int $course_id): bool {
    $stmt = $conn->prepare("SELECT id FROM course WHERE id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    $stmt->close();
    return $exists;
}
