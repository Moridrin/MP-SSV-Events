<?php

namespace mp_ssv_events\CustomPostTypes;

use DateTime;
use mp_ssv_events\SSV_Events;
use mp_ssv_general\forms\models\Forms;
use mp_ssv_general\forms\SSV_Forms;
use wpdb;

if (!defined('ABSPATH')) {
    exit;
}

function show_event(string $content, DateTime $start, DateTime $end, array $tickets)
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
            <form id="ticket_<?= $ticket->t_id ?>" method="post">
                <h3><?= $ticket->t_title ?></h3>
                <?= Forms::getFormFieldsHTML($wpdb->get_row("SELECT * FROM $tableName WHERE f_id = '$ticket->t_f_id'")) ?>
                <?= wp_nonce_field(SSV_Events::TICKET_FORM_REFERER); ?>
                <button type="submit" name="ticket" value="<?= $ticket->t_id ?>">Submit</button>
            </form>
        <?php endforeach; ?>
    </div>
    <?php
}
