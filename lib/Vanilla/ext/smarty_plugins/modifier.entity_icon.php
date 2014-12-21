<?php
function smarty_modifier_entity_icon($id)
{
    $icon = 'icon-question-sign';

    switch($id)
    {
        case 1:
            $icon = 'icon-globe';
            break;
        case 2:
            $icon = 'icon-file';
            break;
        case 3:
            $icon = 'icon-folder-close';
            break;
        case 4:
            $icon = 'icon-briefcase';
            break;
        case 5:
            $icon = 'icon-list';
            break;
        case 6:
            $icon = 'icon-picture';
            break;
        case 7:
            $icon = 'icon-font';
            break;
        case 8:
            $icon = 'icon-calendar';
            break;
        case 10:
            $icon = 'icon-list-alt';
            break;
        case 12:
            $icon = 'icon-eye-open';
            break;
        case 13:
            $icon = 'icon-user';
            break;
        case 14:
            $icon = 'icon-film';
            break;
        case 15:
            $icon = 'icon-music';
            break;
        case 16:
            $icon = 'icon-book';
            break;
        case 17:
            $icon = 'icon-align-left';
            break;
        case 18:
            $icon = 'icon-th-large';
            break;
    }

    return $icon;
}