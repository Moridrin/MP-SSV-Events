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
                    <tr id="<?= $ticket->t_id ?>_tr" draggable="true" class="formField" data-base-field-name="<?= $ticket->t_name ?>" data-input-type="<?= $ticket->t_inputType ?>" data-options='<?= $ticket->t_options ?>' data-properties='<?= json_encode($properties) ?>'>
                        <td>
                            <input type="hidden" name="form_fields[]" value="<?= $ticket->t_name ?>">
                            <strong id="<?= $ticket->t_id ?>_title"><?= $ticket->t_title ?></strong>
                            <?php if ($ticket->t_inputType !== 'hidden'): ?>
                                <span class="inline-actions"> | <a href="javascript:void(0)" onclick="fieldsCustomizer.inlineEdit('<?= $ticket->t_id ?>')" class="editinline" aria-label="Quick edit “<?= $ticket->t_title ?>” inline">Quick Edit</a></span>
                            <?php endif; ?>
                        </td>
                        <td id="<?= $ticket->t_id ?>_inputType"><?= $ticket->t_inputType ?></td>
                        <?php if ($ticket->t_inputType !== 'hidden'): ?>
                            <td id="<?= $ticket->t_id ?>_defaultValue"><?= $properties['defaultValue'] ?></td>
                        <?php else: ?>
                            <td id="<?= $ticket->t_id ?>_value"><?= $ticket->t_value ?></td>
                        <?php endif; ?>
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
