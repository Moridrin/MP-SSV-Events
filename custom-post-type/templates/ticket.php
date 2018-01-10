<?php

namespace mp_ssv_events\CustomPostTypes;

use DateTime;
use mp_ssv_general\forms\options\Forms;
use mp_ssv_general\forms\SSV_Forms;
use wpdb;

if (!defined('ABSPATH')) {
    exit;
}

function show_ticket(string $content, DateTime $start, DateTime $end, array $tickets)
{
    /** @var wpdb $wpdb */
    global $wpdb;
    $tableName = SSV_Forms::SITE_SPECIFIC_FORMS_TABLE;
    ?>
    <h1>About</h1>
    <div id="about">
        <table>
            <tr>
                <th>Start</th>
                <th>End</th>
            </tr>
            <tr>
                <td><?= $start->format('Y-m-d H:i') ?></td>
                <td><?= $end->format('Y-m-d H:i') ?></td>
            </tr>
        </table>
    </div>
    <div id="post-content">
        <?= $content ?>
    </div>
    <h1>Tickets</h1>
    <div id="tickets">
        <?php foreach ($tickets as $ticket): ?>
            <div id="ticket_<?= $ticket->t_id ?>">
                <h3><?= $ticket->t_title ?></h3>
                <?= Forms::getFormHTML($wpdb->get_row("SELECT * FROM $tableName WHERE f_id = '$ticket->t_f_id'")) ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}
