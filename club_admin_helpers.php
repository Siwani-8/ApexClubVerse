<?php
/**
 * Shared helpers for club-specific admin authorization.
 */

function is_club_admin(): bool
{
    return isset($_SESSION['user_role'])
        && $_SESSION['user_role'] === 'admin'
        && !empty($_SESSION['club_id']);
}

function admin_club_id(): int
{
    return is_club_admin() ? (int)$_SESSION['club_id'] : 0;
}

/** Keywords used in registrations.selected_club checkbox values per club id. */
function club_registration_keyword(int $club_id): string
{
    $map = [
        1 => 'Performing Arts',
        2 => 'Sports and Leadership',
        3 => 'Travel and Tourism',
        4 => 'Media and Marketing',
        5 => 'IT Club',
        6 => 'HEAT',
    ];

    return $map[$club_id] ?? '';
}

function event_belongs_to_club(mysqli $conn, int $event_id, int $club_id): bool
{
    $event_id = (int)$event_id;
    $club_id = (int)$club_id;
    $res = mysqli_query($conn, "SELECT id FROM events WHERE id = $event_id AND club_id = $club_id LIMIT 1");
    return $res && mysqli_num_rows($res) > 0;
}

function poll_belongs_to_club(mysqli $conn, int $poll_id, int $club_id): bool
{
    $poll_id = (int)$poll_id;
    $club_id = (int)$club_id;
    $res = mysqli_query($conn, "SELECT id FROM polls WHERE id = $poll_id AND club_id = $club_id LIMIT 1");
    return $res && mysqli_num_rows($res) > 0;
}

function registration_belongs_to_club(mysqli $conn, int $reg_id, int $club_id): bool
{
    $keyword = mysqli_real_escape_string($conn, club_registration_keyword($club_id));
    if ($keyword === '') {
        return false;
    }
    $reg_id = (int)$reg_id;
    $res = mysqli_query($conn, "SELECT id FROM registrations WHERE id = $reg_id AND selected_club LIKE '%$keyword%' LIMIT 1");
    return $res && mysqli_num_rows($res) > 0;
}

function bod_belongs_to_club(mysqli $conn, int $member_id, int $club_id): bool
{
    $member_id = (int)$member_id;
    $club_id = (int)$club_id;
    $res = mysqli_query($conn, "SELECT id FROM bod_members WHERE id = $member_id AND club_id = $club_id LIMIT 1");
    return $res && mysqli_num_rows($res) > 0;
}

function boa_belongs_to_club(mysqli $conn, int $member_id, int $club_id): bool
{
    $member_id = (int)$member_id;
    $club_id = (int)$club_id;
    $res = mysqli_query($conn, "SELECT id FROM boa_members WHERE id = $member_id AND club_id = $club_id LIMIT 1");
    return $res && mysqli_num_rows($res) > 0;
}
