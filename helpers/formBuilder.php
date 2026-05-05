<?php
// helper file that will store modular pages for future use
// will be related to each type of account for better page readability in the future

function registerExhibitorForm($targetPage) {
    $form = '
    <form method="POST" action="' . htmlspecialchars($targetPage) . '">
        <div>
            <label for="org_name"> Organization Name: </label>
            <input type="text" name="org_name" id="org_name" required="required">
        </div>
        <div>
            <label for="org_industry"> Organization Industry: </label>
            <input type="text" name="org_industry" id="org_industry">
        </div>
        <div>
            <button type="submit">Submit</button>
        </div>
    </form>
    ';
    
    return $form;
}

function registerSpeakerForm($targetPage) {
    $form = '
    <form method="POST" action="' . htmlspecialchars($targetPage) . '">
        <div>
            <button type="submit">Register as a Speaker</button>
        </div>
    </form>
    ';
    
    return $form;
}

function registerOrganizerForm($targetPage) {
    $form = '
    <form method="POST" action="' . htmlspecialchars($targetPage) . '">
        <div>
            <button type="submit">Register as a Organizer</button>
        </div>
    </form>
    ';
    
    return $form;
}
?>