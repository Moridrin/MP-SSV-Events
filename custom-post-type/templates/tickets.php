<?php

namespace mp_ssv_events\CustomPostTypes;

if (!defined('ABSPATH')) {
    exit;
}

function show_tickets_table(array $tickets)
{
    ?>
    <div style="overflow-x: auto;">
        <table id="ticketsContainer" class="wp-list-table widefat striped">
            <thead>
            <tr id="ticketsListTop">
                <th scope="col" class="manage-column column-author">Title</th>
                <th scope="col" class="manage-column column-author">Date/Time</th>
                <th scope="col" class="manage-column column-author">Price</th>
            </tr>
            </thead>
            <tbody id="the-list">
            <?php if (!empty($tickets)): ?>
                <?php foreach ($tickets as $ticket): ?>
                    <?php
                    $properties = [
                        'title'         => $ticket->t_title,
                        'dateTimeStart' => $ticket->t_start,
                        'dateTimeEnd'   => $ticket->t_end,
                        'price'         => $ticket->t_price,
                    ];
                    ?>
                    <tr id="<?= $ticket->t_id ?>_tr" draggable="true" class="formField" data-properties='<?= json_encode($properties) ?>'>
                        <td>
                            <strong id="<?= $ticket->t_id ?>_title"><?= $ticket->t_title ?></strong>
                            <span class="inline-actions"> | <a href="javascript:void(0)" onclick="ticketsManager.inlineEdit('<?= $ticket->t_id ?>')" class="editinline" aria-label="Quick edit “<?= $ticket->t_title ?>” inline">Quick Edit</a></span>
                        </td>
                        <td id="<?= $ticket->t_id ?>_dateTime"><?= $ticket->t_start ?> - <?= $ticket->t_end ?></td>
                        <td id="<?= $ticket->t_id ?>_price"><?= $ticket->t_price ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr id="no-items" class="no-items">
                    <td class="colspanchange" colspan="3">There are no tickets for this event yet.</td>
                </tr>
            <?php endif; ?>
            </tbody>
            <tfoot>
            <tr id="ticketsListBottom">
                <th scope="col" class="manage-column column-author">Title</th>
                <th scope="col" class="manage-column column-author">Date/Time</th>
                <th scope="col" class="manage-column column-author">Price</th>
            </tr>
            </tfoot>
        </table>
    </div>
    <?php
}
